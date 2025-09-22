<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Models\WellsoftModule;
use App\Services\StatusMigrationService;
use App\Traits\HandlesStatusQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ModuleRequestController extends Controller
{
    use HandlesStatusQueries;
    
    protected StatusMigrationService $statusMigrationService;
    
    public function __construct(StatusMigrationService $statusMigrationService)
    {
        $this->statusMigrationService = $statusMigrationService;
    }
    /**
     * Store a new module request.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            
            // Validate the request
            $validatedData = $request->validate([
                'user_access_id' => 'required|exists:user_access,id',
                'module_requested_for' => 'required|string|in:use,revoke',
                'wellsoft_modules' => 'required|array|min:1',
                'wellsoft_modules.*' => 'required|string|exists:wellsoft_modules,name',
            ], [
                'module_requested_for.required' => 'Please select whether you want to use or revoke modules.',
                'module_requested_for.in' => 'Module request type must be either "use" or "revoke".',
                'wellsoft_modules.required' => 'Please select at least one Wellsoft module.',
                'wellsoft_modules.min' => 'Please select at least one Wellsoft module.',
                'wellsoft_modules.*.exists' => 'One or more selected modules are invalid.',
            ]);

            Log::info('🔄 PROCESSING MODULE REQUEST', [
                'user_id' => $user->id,
                'user_access_id' => $validatedData['user_access_id'],
                'module_requested_for' => $validatedData['module_requested_for'],
                'wellsoft_modules_count' => count($validatedData['wellsoft_modules']),
                'wellsoft_modules' => $validatedData['wellsoft_modules']
            ]);

            // Get the user access record
            $userAccess = UserAccess::findOrFail($validatedData['user_access_id']);
            
            // Check if the user owns this request or has permission to modify it
            if ($userAccess->user_id !== $user->id && !$user->hasRole(['admin', 'ict_officer'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to modify this request.'
                ], 403);
            }

            // Update the user_access record with module_requested_for
            $userAccess->update([
                'module_requested_for' => $validatedData['module_requested_for']
            ]);

            // Get the module IDs from the module names
            $moduleIds = WellsoftModule::whereIn('name', $validatedData['wellsoft_modules'])
                                     ->where('is_active', true)
                                     ->pluck('id', 'name')
                                     ->toArray();

            // Validate that all requested modules exist and are active
            $missingModules = array_diff($validatedData['wellsoft_modules'], array_keys($moduleIds));
            if (!empty($missingModules)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Some selected modules are not available: ' . implode(', ', $missingModules),
                    'errors' => [
                        'wellsoft_modules' => ['Some selected modules are not available.']
                    ]
                ], 422);
            }

            // Clear existing module selections for this request
            $userAccess->selectedWellsoftModules()->detach();

            // Attach the new module selections
            $userAccess->selectedWellsoftModules()->attach(array_values($moduleIds));

            Log::info('✅ MODULE REQUEST STORED SUCCESSFULLY', [
                'user_access_id' => $userAccess->id,
                'module_requested_for' => $userAccess->module_requested_for,
                'modules_attached' => count($moduleIds),
                'module_ids' => array_values($moduleIds)
            ]);

            DB::commit();

            // Load the relationships for response
            $userAccess->load(['selectedWellsoftModules', 'user', 'department']);

            return response()->json([
                'status' => 'success',
                'message' => 'Module request stored successfully',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'module_requested_for' => $userAccess->module_requested_for,
                    'selected_modules' => $userAccess->selectedWellsoftModules->pluck('name')->toArray(),
                    'selected_modules_count' => $userAccess->selectedWellsoftModules->count(),
                    'request_status' => $userAccess->status, // Legacy status for backward compatibility
                    
                    // New granular status information
                    'workflow_status' => [
                        'hod_status' => $userAccess->hod_status,
                        'divisional_status' => $userAccess->divisional_status,
                        'ict_director_status' => $userAccess->ict_director_status,
                        'head_it_status' => $userAccess->head_it_status,
                        'ict_officer_status' => $userAccess->ict_officer_status
                    ],
                    'current_stage' => $this->determineCurrentWorkflowStage($userAccess),
                    'can_be_modified' => $userAccess->hod_status === 'pending', // Only editable if not yet approved by HOD
                    
                    'user_name' => $userAccess->user->name,
                    'department_name' => $userAccess->department->name ?? 'Unknown'
                ]
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error storing module request', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store module request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get available Wellsoft modules.
     */
    public function getAvailableModules(): JsonResponse
    {
        try {
            $modules = WellsoftModule::active()
                                   ->orderBy('name')
                                   ->get(['id', 'name', 'description']);

            return response()->json([
                'status' => 'success',
                'data' => $modules,
                'count' => $modules->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching available modules', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch available modules.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get module request details for a specific user access record.
     */
    public function show(int $userAccessId): JsonResponse
    {
        try {
            $userAccess = UserAccess::with(['selectedWellsoftModules', 'user', 'department'])
                                  ->findOrFail($userAccessId);

            $user = request()->user();
            
            // Check if the user has permission to view this request
            if ($userAccess->user_id !== $user->id && !$user->hasRole(['admin', 'ict_officer', 'head_of_department', 'divisional_director'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to view this request.'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'module_requested_for' => $userAccess->module_requested_for,
                    'selected_modules' => $userAccess->selectedWellsoftModules->map(function ($module) {
                        return [
                            'id' => $module->id,
                            'name' => $module->name,
                            'description' => $module->description
                        ];
                    }),
                    'selected_modules_count' => $userAccess->selectedWellsoftModules->count(),
                    'request_status' => $userAccess->status, // Legacy status for backward compatibility
                    
                    // New granular status information
                    'workflow_status' => [
                        'hod_status' => $userAccess->hod_status,
                        'divisional_status' => $userAccess->divisional_status,
                        'ict_director_status' => $userAccess->ict_director_status,
                        'head_it_status' => $userAccess->head_it_status,
                        'ict_officer_status' => $userAccess->ict_officer_status
                    ],
                    'current_stage' => $this->determineCurrentWorkflowStage($userAccess),
                    'can_be_modified' => $userAccess->hod_status === 'pending', // Only editable if not yet approved by HOD
                    'approval_history' => $this->getApprovalHistory($userAccess),
                    
                    'user_info' => [
                        'id' => $userAccess->user->id,
                        'name' => $userAccess->user->name,
                        'pf_number' => $userAccess->pf_number,
                        'staff_name' => $userAccess->staff_name
                    ],
                    'department_info' => [
                        'id' => $userAccess->department->id ?? null,
                        'name' => $userAccess->department->name ?? 'Unknown'
                    ],
                    'created_at' => $userAccess->created_at,
                    'updated_at' => $userAccess->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching module request details', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch module request details.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update an existing module request.
     */
    public function update(Request $request, int $userAccessId): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            
            // Validate the request
            $validatedData = $request->validate([
                'module_requested_for' => 'required|string|in:use,revoke',
                'wellsoft_modules' => 'required|array|min:1',
                'wellsoft_modules.*' => 'required|string|exists:wellsoft_modules,name',
            ], [
                'module_requested_for.required' => 'Please select whether you want to use or revoke modules.',
                'module_requested_for.in' => 'Module request type must be either "use" or "revoke".',
                'wellsoft_modules.required' => 'Please select at least one Wellsoft module.',
                'wellsoft_modules.min' => 'Please select at least one Wellsoft module.',
                'wellsoft_modules.*.exists' => 'One or more selected modules are invalid.',
            ]);

            // Get the user access record
            $userAccess = UserAccess::findOrFail($userAccessId);
            
            // Check if the user owns this request or has permission to modify it
            if ($userAccess->user_id !== $user->id && !$user->hasRole(['admin', 'ict_officer'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to modify this request.'
                ], 403);
            }

            // Check if the request can be updated using new granular status system
            // Only allow updates if the request hasn't entered the approval workflow yet
            if ($userAccess->hod_status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This request cannot be updated as it has already entered the approval workflow.',
                    'error' => 'Request is no longer modifiable',
                    'current_workflow_stage' => [
                        'hod_status' => $userAccess->hod_status,
                        'divisional_status' => $userAccess->divisional_status,
                        'ict_director_status' => $userAccess->ict_director_status,
                        'head_it_status' => $userAccess->head_it_status,
                        'ict_officer_status' => $userAccess->ict_officer_status
                    ]
                ], 403);
            }

            Log::info('🔄 UPDATING MODULE REQUEST', [
                'user_id' => $user->id,
                'user_access_id' => $userAccessId,
                'module_requested_for' => $validatedData['module_requested_for'],
                'wellsoft_modules_count' => count($validatedData['wellsoft_modules']),
                'wellsoft_modules' => $validatedData['wellsoft_modules']
            ]);

            // Update the user_access record with module_requested_for
            $userAccess->update([
                'module_requested_for' => $validatedData['module_requested_for']
            ]);

            // Get the module IDs from the module names
            $moduleIds = WellsoftModule::whereIn('name', $validatedData['wellsoft_modules'])
                                     ->where('is_active', true)
                                     ->pluck('id', 'name')
                                     ->toArray();

            // Validate that all requested modules exist and are active
            $missingModules = array_diff($validatedData['wellsoft_modules'], array_keys($moduleIds));
            if (!empty($missingModules)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Some selected modules are not available: ' . implode(', ', $missingModules),
                    'errors' => [
                        'wellsoft_modules' => ['Some selected modules are not available.']
                    ]
                ], 422);
            }

            // Clear existing module selections for this request
            $userAccess->selectedWellsoftModules()->detach();

            // Attach the new module selections
            $userAccess->selectedWellsoftModules()->attach(array_values($moduleIds));

            Log::info('✅ MODULE REQUEST UPDATED SUCCESSFULLY', [
                'user_access_id' => $userAccess->id,
                'module_requested_for' => $userAccess->module_requested_for,
                'modules_attached' => count($moduleIds),
                'module_ids' => array_values($moduleIds)
            ]);

            DB::commit();

            // Load the relationships for response
            $userAccess->load(['selectedWellsoftModules', 'user', 'department']);

            return response()->json([
                'status' => 'success',
                'message' => 'Module request updated successfully',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'module_requested_for' => $userAccess->module_requested_for,
                    'selected_modules' => $userAccess->selectedWellsoftModules->pluck('name')->toArray(),
                    'selected_modules_count' => $userAccess->selectedWellsoftModules->count(),
                    'request_status' => $userAccess->status, // Legacy status for backward compatibility
                    
                    // New granular status information
                    'workflow_status' => [
                        'hod_status' => $userAccess->hod_status,
                        'divisional_status' => $userAccess->divisional_status,
                        'ict_director_status' => $userAccess->ict_director_status,
                        'head_it_status' => $userAccess->head_it_status,
                        'ict_officer_status' => $userAccess->ict_officer_status
                    ],
                    'current_stage' => $this->determineCurrentWorkflowStage($userAccess),
                    'can_be_modified' => $userAccess->hod_status === 'pending', // Only editable if not yet approved by HOD
                    
                    'user_name' => $userAccess->user->name,
                    'department_name' => $userAccess->department->name ?? 'Unknown'
                ]
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating module request', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'user_access_id' => $userAccessId,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update module request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    
    /**
     * Determine the current workflow stage based on granular status columns
     */
    private function determineCurrentWorkflowStage(UserAccess $userAccess): string
    {
        if ($userAccess->ict_officer_status === 'implemented') {
            return 'completed';
        }
        
        if ($userAccess->hod_status === 'rejected' || 
            $userAccess->divisional_status === 'rejected' ||
            $userAccess->ict_director_status === 'rejected' ||
            $userAccess->head_it_status === 'rejected' ||
            $userAccess->ict_officer_status === 'rejected') {
            return 'rejected';
        }
        
        if ($userAccess->head_it_status === 'approved' && $userAccess->ict_officer_status === 'pending') {
            return 'pending_ict_officer';
        }
        
        if ($userAccess->ict_director_status === 'approved' && $userAccess->head_it_status === 'pending') {
            return 'pending_head_it';
        }
        
        if ($userAccess->divisional_status === 'approved' && $userAccess->ict_director_status === 'pending') {
            return 'pending_ict_director';
        }
        
        if ($userAccess->hod_status === 'approved' && $userAccess->divisional_status === 'pending') {
            return 'pending_divisional';
        }
        
        if ($userAccess->hod_status === 'pending') {
            return 'pending_hod';
        }
        
        return 'unknown';
    }
    
    /**
     * Get approval history information
     */
    private function getApprovalHistory(UserAccess $userAccess): array
    {
        return [
            'hod' => [
                'status' => $userAccess->hod_status,
                'approved_by' => $userAccess->hod_name,
                'approved_at' => $userAccess->hod_approved_at,
                'comments' => $userAccess->hod_comments
            ],
            'divisional' => [
                'status' => $userAccess->divisional_status,
                'approved_by' => $userAccess->divisional_name,
                'approved_at' => $userAccess->divisional_approved_at,
                'comments' => $userAccess->divisional_comments
            ],
            'ict_director' => [
                'status' => $userAccess->ict_director_status,
                'approved_by' => $userAccess->dict_name,
                'approved_at' => $userAccess->dict_approved_at,
                'comments' => $userAccess->dict_comments
            ],
            'head_it' => [
                'status' => $userAccess->head_it_status,
                'approved_by' => $userAccess->head_it_name ?? null,
                'approved_at' => $userAccess->head_it_approved_at ?? null,
                'comments' => $userAccess->head_it_comments ?? null
            ],
            'ict_officer' => [
                'status' => $userAccess->ict_officer_status,
                'implemented_by' => $userAccess->ict_officer_name ?? null,
                'implemented_at' => $userAccess->ict_officer_implemented_at ?? null,
                'comments' => $userAccess->ict_officer_comments ?? null
            ]
        ];
    }
    
    /**
     * Get module request statistics using granular status system
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            // Use trait methods for comprehensive statistics
            $systemStats = $this->getSystemStatistics();
            
            // Module-specific statistics
            $moduleStats = [
                'total_requests' => UserAccess::whereNotNull('module_requested_for')->count(),
                'requests_with_modules' => UserAccess::whereNotNull('module_requested_for')
                                                   ->whereHas('selectedWellsoftModules')
                                                   ->count(),
                'use_requests' => UserAccess::where('module_requested_for', 'use')->count(),
                'revoke_requests' => UserAccess::where('module_requested_for', 'revoke')->count(),
                
                // Workflow stage statistics
                'workflow_stages' => [
                    'pending_hod' => $this->getPendingRequestsForStage('hod')
                                         ->whereNotNull('module_requested_for')
                                         ->count(),
                    'pending_divisional' => $this->getPendingRequestsForStage('divisional')
                                                ->whereNotNull('module_requested_for')
                                                ->count(),
                    'pending_ict_director' => $this->getPendingRequestsForStage('ict_director')
                                                 ->whereNotNull('module_requested_for')
                                                 ->count(),
                    'pending_head_it' => $this->getPendingRequestsForStage('head_it')
                                            ->whereNotNull('module_requested_for')
                                            ->count(),
                    'pending_ict_officer' => $this->getPendingRequestsForStage('ict_officer')
                                                ->whereNotNull('module_requested_for')
                                                ->count(),
                ],
                
                // Popular modules statistics
                'popular_modules' => WellsoftModule::withCount('userAccessRequests')
                                                  ->orderByDesc('user_access_requests_count')
                                                  ->limit(10)
                                                  ->get(['name', 'user_access_requests_count']),
                                                  
                // Recent activity
                'recent_activity' => [
                    'today' => UserAccess::whereNotNull('module_requested_for')
                                        ->whereDate('created_at', today())
                                        ->count(),
                    'this_week' => UserAccess::whereNotNull('module_requested_for')
                                            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                                            ->count(),
                    'this_month' => UserAccess::whereNotNull('module_requested_for')
                                             ->whereMonth('created_at', now()->month)
                                             ->whereYear('created_at', now()->year)
                                             ->count(),
                ]
            ];
            
            return response()->json([
                'status' => 'success',
                'message' => 'Module request statistics retrieved successfully',
                'data' => [
                    'system_overview' => $systemStats,
                    'module_statistics' => $moduleStats
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching module request statistics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve module request statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
