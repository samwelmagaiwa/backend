<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Models\Department;
use App\Traits\HandlesStatusQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HodDivisionalRecommendationsController extends Controller
{
    use HandlesStatusQueries;
    /**
     * Get divisional director recommendations for HOD's department
     */
    public function getDivisionalRecommendations(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Verify user is HOD
            if (!array_intersect($userRoles, ['head_of_department'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only Head of Department can view divisional recommendations.'
                ], 403);
            }
            
            Log::info('🔍 HOD DIVISIONAL RECOMMENDATIONS REQUEST', [
                'hod_user_id' => $currentUser->id,
                'hod_name' => $currentUser->name,
                'request_params' => $request->all()
            ]);
            
            // Get HOD's department(s)
            $hodDepartments = Department::where('hod_user_id', $currentUser->id)->get();
            
            if ($hodDepartments->isEmpty()) {
                Log::warning('HOD has no departments assigned', [
                    'hod_user_id' => $currentUser->id,
                    'hod_name' => $currentUser->name
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 10,
                        'total' => 0
                    ],
                    'message' => 'No departments assigned to this HOD'
                ]);
            }
            
            $hodDepartmentIds = $hodDepartments->pluck('id')->toArray();
            
            Log::info('HOD department verification', [
                'hod_department_ids' => $hodDepartmentIds,
                'department_names' => $hodDepartments->pluck('name')->toArray()
            ]);
            
            // Build query for requests from HOD's departments that have divisional director feedback
            $query = UserAccess::with(['user', 'department'])
                ->whereIn('department_id', $hodDepartmentIds)
                ->whereNotNull('divisional_director_comments')
                ->where('divisional_director_comments', '!=', '')
                // Use new status columns - HOD should see requests that have been reviewed by divisional director
                ->where(function($q) {
                    $q->where('divisional_status', 'approved')
                      ->orWhere('divisional_status', 'rejected');
                })
                // Additional filtering for workflow progression - show requests regardless of subsequent stages
                ->where('hod_status', 'approved') // Only show requests that this HOD has approved
                ->orderBy('divisional_approved_at', 'desc')
                ->orderBy('updated_at', 'desc');
            
            // Handle pagination
            $perPage = min($request->get('per_page', 10), 50); // Max 50 items per page
            $page = max($request->get('page', 1), 1);
            
            $paginatedResults = $query->paginate($perPage, ['*'], 'page', $page);
            
            Log::info('✅ Divisional recommendations query results', [
                'total_found' => $paginatedResults->total(),
                'current_page' => $paginatedResults->currentPage(),
                'per_page' => $paginatedResults->perPage(),
                'last_page' => $paginatedResults->lastPage()
            ]);
            
            // Transform the data
            $recommendations = $paginatedResults->items();
            $transformedRecommendations = collect($recommendations)->map(function ($request) {
                return [
                    'id' => $request->id,
                    'pf_number' => $request->pf_number,
                    'staff_name' => $request->staff_name,
                    'phone_number' => $request->phone_number,
                    'department' => [
                        'id' => $request->department->id ?? null,
                        'name' => $request->department->name ?? 'Unknown',
                        'code' => $request->department->code ?? null
                    ],
                    'request_type' => $request->request_type ?? [],
                    'status' => $request->status,
                    'access_type' => $request->access_type,
                    'temporary_until' => $request->temporary_until,
                    'module_requested_for' => $request->module_requested_for,
                    
                    // Module data using the cleaned up column names
                    'wellsoft_modules_selected' => $request->wellsoft_modules_selected ?? [],
                    'jeeva_modules_selected' => $request->jeeva_modules_selected ?? [],
                    'internet_purposes' => $request->internet_purposes ?? [],
                    
                    // Divisional director information
                    'divisional_director_name' => $request->divisional_director_name,
                    'divisional_director_comments' => $request->divisional_director_comments,
                    'divisional_approved_at' => $request->divisional_approved_at,
                    'divisional_director_signature_path' => $request->divisional_director_signature_path,
                    
                    // Additional approval info for context
                    'hod_name' => $request->hod_name,
                    'hod_approved_at' => $request->hod_approved_at,
                    'hod_comments' => $request->hod_comments,
                    
                    // Timestamps
                    'created_at' => $request->created_at,
                    'updated_at' => $request->updated_at
                ];
            });
            
            $response = [
                'success' => true,
                'data' => $transformedRecommendations,
                'pagination' => [
                    'current_page' => $paginatedResults->currentPage(),
                    'last_page' => $paginatedResults->lastPage(),
                    'per_page' => $paginatedResults->perPage(),
                    'total' => $paginatedResults->total(),
                    'from' => $paginatedResults->firstItem(),
                    'to' => $paginatedResults->lastItem()
                ],
                'summary' => [
                    'total_recommendations' => $paginatedResults->total(),
                    'hod_departments' => $hodDepartments->pluck('name')->toArray(),
                    'showing' => count($transformedRecommendations) . ' of ' . $paginatedResults->total()
                ]
            ];
            
            Log::info('✅ Divisional recommendations response prepared', [
                'recommendations_count' => count($transformedRecommendations),
                'pagination_info' => $response['pagination']
            ]);
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('❌ Error fetching divisional recommendations', [
                'error' => $e->getMessage(),
                'hod_user_id' => $request->user()?->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch divisional recommendations: ' . $e->getMessage(),
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0
                ]
            ], 500);
        }
    }
    
    /**
     * Get statistics about divisional recommendations for HOD
     */
    public function getRecommendationStats(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Verify user is HOD
            if (!array_intersect($userRoles, ['head_of_department'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only Head of Department can view recommendation statistics.'
                ], 403);
            }
            
            // Get HOD's department(s)
            $hodDepartments = Department::where('hod_user_id', $currentUser->id)->get();
            
            if ($hodDepartments->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_recommendations' => 0,
                        'recent_recommendations' => 0,
                        'pending_response' => 0,
                        'responded_to' => 0
                    ]
                ]);
            }
            
            $hodDepartmentIds = $hodDepartments->pluck('id')->toArray();
            
            // Get base query for requests with divisional comments
            $baseQuery = UserAccess::whereIn('department_id', $hodDepartmentIds)
                ->whereNotNull('divisional_director_comments')
                ->where('divisional_director_comments', '!=', '');
            
            $stats = [
                'total_recommendations' => (clone $baseQuery)->count(),
                'recent_recommendations' => (clone $baseQuery)
                    ->where('divisional_approved_at', '>=', now()->subDays(7))
                    ->count(),
                'pending_response' => (clone $baseQuery)
                    ->whereNull('hod_approved_at')
                    ->count(),
                'responded_to' => (clone $baseQuery)
                    ->whereNotNull('hod_approved_at')
                    ->count(),
                'departments' => $hodDepartments->pluck('name')->toArray()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error fetching recommendation statistics', [
                'error' => $e->getMessage(),
                'hod_user_id' => $request->user()?->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recommendation statistics',
                'data' => [
                    'total_recommendations' => 0,
                    'recent_recommendations' => 0,
                    'pending_response' => 0,
                    'responded_to' => 0
                ]
            ], 500);
        }
    }

    /**
     * Resubmit a rejected request after addressing divisional director feedback
     */
    public function resubmitRequest(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Verify user is HOD
            if (!array_intersect($userRoles, ['head_of_department'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only Head of Department can resubmit requests.'
                ], 403);
            }
            
            // Validation
            $validated = $request->validate([
                'resubmission_notes' => 'required|string|max:1000',
                'hod_signature' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            ], [
                'resubmission_notes.required' => 'Please provide notes explaining how the divisional director\'s feedback was addressed',
                'hod_signature.mimes' => 'Signature must be in JPEG, JPG, PNG, or PDF format',
                'hod_signature.max' => 'Signature file must not exceed 2MB'
            ]);
            
            // Get user access record
            $userAccess = UserAccess::with('department')->findOrFail($userAccessId);
            
            // Verify this HOD has permission to resubmit this request
            $hodDepartment = Department::where('hod_user_id', $currentUser->id)
                ->where('id', $userAccess->department_id)
                ->first();
                
            if (!$hodDepartment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. You can only resubmit requests from your department.'
                ], 403);
            }
            
            // Verify request can be resubmitted (must be rejected by divisional director using new status columns)
            if ($userAccess->divisional_status !== 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only requests rejected by divisional director can be resubmitted.'
                ], 422);
            }
            
            // Handle signature upload if provided
            $hodSignaturePath = $userAccess->hod_signature_path; // Keep existing if no new one
            if ($request->hasFile('hod_signature')) {
                try {
                    $signatureFile = $request->file('hod_signature');
                    $signatureDir = 'signatures/hod';
                    if (!\Storage::disk('public')->exists($signatureDir)) {
                        \Storage::disk('public')->makeDirectory($signatureDir);
                    }
                    
                    $filename = 'hod_signature_' . $userAccess->pf_number . '_' . time() . '.' . $signatureFile->getClientOriginalExtension();
                    $hodSignaturePath = $signatureFile->storeAs($signatureDir, $filename, 'public');
                    
                } catch (\Exception $e) {
                    Log::error('Failed to upload HOD signature during resubmission', [
                        'error' => $e->getMessage(),
                        'user_access_id' => $userAccessId
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload signature: ' . $e->getMessage()
                    ], 500);
                }
            }
            
            // Update the record to reset it back to pending divisional approval
            $updateData = [
                'hod_resubmission_notes' => $validated['resubmission_notes'],
                'resubmitted_at' => now(),
                'resubmitted_by' => $currentUser->id,
                // Reset new status columns
                'divisional_status' => 'pending', // Reset divisional status to pending for re-review
                // Keep legacy status for backward compatibility during transition
                'status' => 'hod_approved', // Reset to HOD approved so divisional director can review again
            ];
            
            if ($hodSignaturePath) {
                $updateData['hod_signature_path'] = $hodSignaturePath;
            }
            
            // Clear previous divisional director decision but keep comments for reference
            $updateData['divisional_director_signature_path'] = null;
            // Keep divisional_director_comments and divisional_approved_at for history
            
            $userAccess->update($updateData);
            $userAccess->refresh();
            
            Log::info('Request resubmitted successfully', [
                'user_access_id' => $userAccess->id,
                'resubmitted_by' => $currentUser->id,
                'status' => $userAccess->status
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Request has been resubmitted for divisional director review.',
                'data' => [
                    'request_id' => $userAccess->id,
                    'status' => $userAccess->status,
                    'resubmitted_at' => $userAccess->resubmitted_at->format('Y-m-d H:i:s'),
                    'resubmission_notes' => $userAccess->hod_resubmission_notes,
                    'next_step' => 'divisional_director_review'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error resubmitting request', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId,
                'hod_user_id' => $request->user()?->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resubmit request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details for a specific request to help with resubmission
     */
    public function getRequestDetails(Request $request, int $userAccessId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $userRoles = $currentUser->roles()->pluck('name')->toArray();
            
            // Verify user is HOD
            if (!array_intersect($userRoles, ['head_of_department'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only Head of Department can view request details.'
                ], 403);
            }
            
            // Get user access record
            $userAccess = UserAccess::with(['user', 'department'])->findOrFail($userAccessId);
            
            // Verify this HOD has permission to view this request
            $hodDepartment = Department::where('hod_user_id', $currentUser->id)
                ->where('id', $userAccess->department_id)
                ->first();
                
            if (!$hodDepartment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. You can only view requests from your department.'
                ], 403);
            }
            
            $requestDetails = [
                'id' => $userAccess->id,
                'pf_number' => $userAccess->pf_number,
                'staff_name' => $userAccess->staff_name,
                'department' => $userAccess->department->name ?? 'Unknown',
                'phone_number' => $userAccess->phone_number,
                'request_type' => $userAccess->request_type ?? [],
                'status' => $userAccess->status,
                'access_type' => $userAccess->access_type,
                'temporary_until' => $userAccess->temporary_until,
                'module_requested_for' => $userAccess->module_requested_for,
                'wellsoft_modules_selected' => $userAccess->wellsoft_modules_selected ?? [],
                'jeeva_modules_selected' => $userAccess->jeeva_modules_selected ?? [],
                'internet_purposes' => $userAccess->internet_purposes ?? [],
                
                // Original HOD approval
                'hod_approval' => [
                    'name' => $userAccess->hod_name,
                    'approved_at' => $userAccess->hod_approved_at,
                    'comments' => $userAccess->hod_comments,
                    'signature_path' => $userAccess->hod_signature_path,
                    'signature_url' => $userAccess->hod_signature_path ? \Storage::url($userAccess->hod_signature_path) : null
                ],
                
                // Divisional director feedback
                'divisional_feedback' => [
                    'name' => $userAccess->divisional_director_name,
                    'reviewed_at' => $userAccess->divisional_approved_at,
                    'comments' => $userAccess->divisional_director_comments,
                    'status' => str_contains($userAccess->status, 'rejected') ? 'rejected' : 'approved'
                ],
                
                // Resubmission history
                'resubmission' => [
                    'notes' => $userAccess->hod_resubmission_notes,
                    'resubmitted_at' => $userAccess->resubmitted_at,
                    'resubmitted_by' => $userAccess->resubmitted_by
                ],
                
                // Use new status columns to determine if can resubmit
                'can_resubmit' => $userAccess->divisional_status === 'rejected',
                'created_at' => $userAccess->created_at,
                'updated_at' => $userAccess->updated_at
            ];
            
            return response()->json([
                'success' => true,
                'data' => $requestDetails
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching request details', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId,
                'hod_user_id' => $request->user()?->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch request details: ' . $e->getMessage()
            ], 500);
        }
    }
}
