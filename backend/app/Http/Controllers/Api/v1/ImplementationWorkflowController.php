<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Services\UserAccessWorkflowService;
use App\Traits\HandlesStatusQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ImplementationWorkflowController extends Controller
{
    use HandlesStatusQueries;

    protected $workflowService;

    public function __construct(UserAccessWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }
    /**
     * Store implementation workflow data (Head of IT and ICT Officer).
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            
            // Validate the request
            $validatedData = $request->validate([
                'user_access_id' => 'required|exists:user_access,id',
                
                // Head of IT approval validation
                'head_it_name' => 'sometimes|string|max:255',
                'head_it_signature' => 'sometimes|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'head_it_date' => 'sometimes|date',
                'head_it_comments' => 'sometimes|string|max:1000',
                
                // ICT Officer implementation validation
                'ict_officer_name' => 'sometimes|string|max:255',
                'ict_officer_signature' => 'sometimes|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'ict_officer_date' => 'sometimes|date',
                'ict_officer_comments' => 'sometimes|string|max:1000',
                'implementation_comments' => 'sometimes|string|max:1000',
            ], [
                'user_access_id.required' => 'User access request ID is required.',
                'user_access_id.exists' => 'Invalid user access request.',
                '*.mimes' => 'Signature files must be in JPEG, JPG, PNG, or PDF format.',
                '*.max' => 'Signature files must not exceed 2MB.',
                'head_it_name.max' => 'Head of IT name must not exceed 255 characters.',
                'ict_officer_name.max' => 'ICT Officer name must not exceed 255 characters.',
                '*.string' => 'Comments must be text.',
            ]);

            Log::info('🔄 PROCESSING IMPLEMENTATION WORKFLOW', [
                'user_id' => $user->id,
                'user_access_id' => $validatedData['user_access_id'],
                'has_head_it_data' => isset($validatedData['head_it_name']),
                'has_ict_officer_data' => isset($validatedData['ict_officer_name'])
            ]);

            // Get the user access record
            $userAccess = UserAccess::findOrFail($validatedData['user_access_id']);
            
            // Check if the user has permission to modify this request
            if (!$user->hasRole(['admin', 'ict_officer', 'head_of_department', 'divisional_director', 'ict_director', 'head_it'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to modify this request.'
                ], 403);
            }

            // Prepare update data
            $updateData = [];

            // Handle Head of IT approval data
            if (isset($validatedData['head_it_name'])) {
                $updateData['head_it_name'] = $validatedData['head_it_name'];
                $updateData['head_it_comments'] = $validatedData['head_it_comments'] ?? null;
                $updateData['head_it_approved_at'] = isset($validatedData['head_it_date']) ? $validatedData['head_it_date'] : now();
                
                // Handle Head of IT signature upload
                if ($request->hasFile('head_it_signature')) {
                    $headItSignaturePath = $this->handleSignatureUpload(
                        $request->file('head_it_signature'),
                        'head_it',
                        $userAccess->pf_number
                    );
                    $updateData['head_it_signature_path'] = $headItSignaturePath;
                }

                Log::info('✅ Head of IT approval data processed', [
                    'head_it_name' => $updateData['head_it_name'],
                    'has_signature' => isset($updateData['head_it_signature_path'])
                ]);
            }

            // Handle ICT Officer implementation data
            if (isset($validatedData['ict_officer_name'])) {
                $updateData['ict_officer_name'] = $validatedData['ict_officer_name'];
                $updateData['ict_officer_comments'] = $validatedData['ict_officer_comments'] ?? null;
                $updateData['ict_officer_implemented_at'] = isset($validatedData['ict_officer_date']) ? $validatedData['ict_officer_date'] : now();
                
                // Handle ICT Officer signature upload
                if ($request->hasFile('ict_officer_signature')) {
                    $ictOfficerSignaturePath = $this->handleSignatureUpload(
                        $request->file('ict_officer_signature'),
                        'ict_officer',
                        $userAccess->pf_number
                    );
                    $updateData['ict_officer_signature_path'] = $ictOfficerSignaturePath;
                }

                // Handle general implementation comments
                if (isset($validatedData['implementation_comments'])) {
                    $updateData['implementation_comments'] = $validatedData['implementation_comments'];
                }

                Log::info('✅ ICT Officer implementation data processed', [
                    'ict_officer_name' => $updateData['ict_officer_name'],
                    'has_signature' => isset($updateData['ict_officer_signature_path']),
                    'has_implementation_comments' => isset($updateData['implementation_comments'])
                ]);
            }

            // Update the user access record
            if (!empty($updateData)) {
                $userAccess->update($updateData);

                // Update status based on completion
                $this->updateRequestStatus($userAccess);
            }

            Log::info('✅ IMPLEMENTATION WORKFLOW STORED SUCCESSFULLY', [
                'user_access_id' => $userAccess->id,
                'head_it_approved' => !empty($userAccess->head_it_name),
                'ict_officer_implemented' => !empty($userAccess->ict_officer_name),
                'status' => $userAccess->status
            ]);

            DB::commit();

            // Load the relationships for response
            $userAccess->load(['user', 'department']);

            return response()->json([
                'status' => 'success',
                'message' => 'Implementation approvals stored successfully',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'implementation_status' => [
                        'head_it_approved' => !empty($userAccess->head_it_name),
                        'head_it_approved_at' => $userAccess->head_it_approved_at,
                        'ict_officer_implemented' => !empty($userAccess->ict_officer_name),
                        'ict_officer_implemented_at' => $userAccess->ict_officer_implemented_at,
                        'implementation_complete' => $this->isImplementationComplete($userAccess),
                    ],
                    'request_status' => $userAccess->status,
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
            
            Log::error('Error storing implementation workflow', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'request_data' => $request->except(['head_it_signature', 'ict_officer_signature'])
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store implementation workflow.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get implementation workflow details for a specific user access record.
     */
    public function show(int $userAccessId): JsonResponse
    {
        try {
            $userAccess = UserAccess::with(['user', 'department'])
                                  ->findOrFail($userAccessId);

            $user = request()->user();
            
            // Check if the user has permission to view this request
            if (!$user->hasRole(['admin', 'ict_officer', 'head_of_department', 'divisional_director', 'ict_director', 'head_it'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to view this request.'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'implementation_workflow' => [
                        'head_it' => [
                            'name' => $userAccess->head_it_name,
                            'signature_path' => $userAccess->head_it_signature_path,
                            'approved_at' => $userAccess->head_it_approved_at,
                            'comments' => $userAccess->head_it_comments,
                        ],
                        'ict_officer' => [
                            'name' => $userAccess->ict_officer_name,
                            'signature_path' => $userAccess->ict_officer_signature_path,
                            'implemented_at' => $userAccess->ict_officer_implemented_at,
                            'comments' => $userAccess->ict_officer_comments,
                        ],
                        'implementation_comments' => $userAccess->implementation_comments,
                        'implementation_complete' => $this->isImplementationComplete($userAccess),
                    ],
                    'request_status' => $userAccess->status,
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
            Log::error('Error fetching implementation workflow details', [
                'error' => $e->getMessage(),
                'user_access_id' => $userAccessId
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch implementation workflow details.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update an existing implementation workflow.
     */
    public function update(Request $request, int $userAccessId): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            
            // Validate the request
            $validatedData = $request->validate([
                // Head of IT approval validation
                'head_it_name' => 'sometimes|string|max:255',
                'head_it_signature' => 'sometimes|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'head_it_date' => 'sometimes|date',
                'head_it_comments' => 'sometimes|string|max:1000',
                
                // ICT Officer implementation validation
                'ict_officer_name' => 'sometimes|string|max:255',
                'ict_officer_signature' => 'sometimes|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'ict_officer_date' => 'sometimes|date',
                'ict_officer_comments' => 'sometimes|string|max:1000',
                'implementation_comments' => 'sometimes|string|max:1000',
            ], [
                '*.mimes' => 'Signature files must be in JPEG, JPG, PNG, or PDF format.',
                '*.max' => 'Signature files must not exceed 2MB.',
                'head_it_name.max' => 'Head of IT name must not exceed 255 characters.',
                'ict_officer_name.max' => 'ICT Officer name must not exceed 255 characters.',
            ]);

            // Get the user access record
            $userAccess = UserAccess::findOrFail($userAccessId);
            
            // Check if the user has permission to modify this request
            if (!$user->hasRole(['admin', 'ict_officer', 'head_of_department', 'divisional_director', 'ict_director', 'head_it'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to modify this request.'
                ], 403);
            }

            Log::info('🔄 UPDATING IMPLEMENTATION WORKFLOW', [
                'user_id' => $user->id,
                'user_access_id' => $userAccessId,
                'has_head_it_data' => isset($validatedData['head_it_name']),
                'has_ict_officer_data' => isset($validatedData['ict_officer_name'])
            ]);

            // Prepare update data (same logic as store method)
            $updateData = [];

            // Handle Head of IT approval data
            if (isset($validatedData['head_it_name'])) {
                $updateData['head_it_name'] = $validatedData['head_it_name'];
                $updateData['head_it_comments'] = $validatedData['head_it_comments'] ?? null;
                $updateData['head_it_approved_at'] = isset($validatedData['head_it_date']) ? $validatedData['head_it_date'] : now();
                
                if ($request->hasFile('head_it_signature')) {
                    $headItSignaturePath = $this->handleSignatureUpload(
                        $request->file('head_it_signature'),
                        'head_it',
                        $userAccess->pf_number
                    );
                    $updateData['head_it_signature_path'] = $headItSignaturePath;
                }
            }

            // Handle ICT Officer implementation data
            if (isset($validatedData['ict_officer_name'])) {
                $updateData['ict_officer_name'] = $validatedData['ict_officer_name'];
                $updateData['ict_officer_comments'] = $validatedData['ict_officer_comments'] ?? null;
                $updateData['ict_officer_implemented_at'] = isset($validatedData['ict_officer_date']) ? $validatedData['ict_officer_date'] : now();
                
                if ($request->hasFile('ict_officer_signature')) {
                    $ictOfficerSignaturePath = $this->handleSignatureUpload(
                        $request->file('ict_officer_signature'),
                        'ict_officer',
                        $userAccess->pf_number
                    );
                    $updateData['ict_officer_signature_path'] = $ictOfficerSignaturePath;
                }

                if (isset($validatedData['implementation_comments'])) {
                    $updateData['implementation_comments'] = $validatedData['implementation_comments'];
                }
            }

            // Update the user access record
            if (!empty($updateData)) {
                $userAccess->update($updateData);
                $this->updateRequestStatus($userAccess);
            }

            Log::info('✅ IMPLEMENTATION WORKFLOW UPDATED SUCCESSFULLY', [
                'user_access_id' => $userAccess->id,
                'head_it_approved' => !empty($userAccess->head_it_name),
                'ict_officer_implemented' => !empty($userAccess->ict_officer_name)
            ]);

            DB::commit();

            // Load the relationships for response
            $userAccess->load(['user', 'department']);

            return response()->json([
                'status' => 'success',
                'message' => 'Implementation workflow updated successfully',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'implementation_status' => [
                        'head_it_approved' => !empty($userAccess->head_it_name),
                        'head_it_approved_at' => $userAccess->head_it_approved_at,
                        'ict_officer_implemented' => !empty($userAccess->ict_officer_name),
                        'ict_officer_implemented_at' => $userAccess->ict_officer_implemented_at,
                        'implementation_complete' => $this->isImplementationComplete($userAccess),
                    ],
                    'request_status' => $userAccess->status,
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
            
            Log::error('Error updating implementation workflow', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'user_access_id' => $userAccessId,
                'request_data' => $request->except(['head_it_signature', 'ict_officer_signature'])
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update implementation workflow.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Handle signature file upload.
     */
    private function handleSignatureUpload($file, string $type, string $pfNumber): string
    {
        $filename = "{$type}_signature_{$pfNumber}_" . time() . "." . $file->getClientOriginalExtension();
        $path = $file->storeAs("signatures/{$type}", $filename, 'public');
        
        Log::info("✅ Signature uploaded successfully", [
            'type' => $type,
            'filename' => $filename,
            'path' => $path
        ]);
        
        return $path;
    }

    /**
     * Check if implementation is complete.
     */
    private function isImplementationComplete(UserAccess $userAccess): bool
    {
        return !empty($userAccess->head_it_name) && 
               !empty($userAccess->head_it_signature_path) && 
               !empty($userAccess->head_it_approved_at) &&
               !empty($userAccess->ict_officer_name) && 
               !empty($userAccess->ict_officer_signature_path) && 
               !empty($userAccess->ict_officer_implemented_at);
    }

    /**
     * Update request status based on implementation progress.
     */
    private function updateRequestStatus(UserAccess $userAccess): void
    {
        if ($this->isImplementationComplete($userAccess)) {
            // Set ICT Officer status to implemented
            $userAccess->update([
                'ict_officer_status' => 'implemented',
                'status' => 'implemented'
            ]);
            Log::info('✅ Request status updated to implemented', [
                'user_access_id' => $userAccess->id
            ]);
        } elseif (!empty($userAccess->head_it_name)) {
            // Head IT approved, now pending ICT Officer implementation
            $userAccess->update([
                'head_it_status' => 'approved',
                'ict_officer_status' => 'pending',
                'status' => 'pending_ict_officer'
            ]);
            Log::info('✅ Request status updated to pending ICT officer', [
                'user_access_id' => $userAccess->id
            ]);
        } elseif (!empty($userAccess->ict_director_name)) {
            // ICT Director approved, now pending Head IT
            $userAccess->update([
                'ict_director_status' => 'approved',
                'head_it_status' => 'pending',
                'status' => 'pending_head_it'
            ]);
            Log::info('✅ Request status updated to pending Head of IT', [
                'user_access_id' => $userAccess->id
            ]);
        }
    }

    /**
     * Get requests pending Head IT approval.
     */
    public function getPendingHeadItRequests(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole(['admin', 'head_it'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to view these requests.'
                ], 403);
            }

            $requests = $this->getPendingRequestsForStage('head_it')
                ->with(['user', 'department'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $requests->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'user_name' => $request->user->name,
                        'pf_number' => $request->pf_number,
                        'department_name' => $request->department->name ?? 'Unknown',
                        'request_type' => $request->request_type,
                        'created_at' => $request->created_at,
                        'updated_at' => $request->updated_at,
                        'workflow_progress' => $request->getWorkflowProgressFromColumns()
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching pending Head IT requests', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch pending Head IT requests.'
            ], 500);
        }
    }

    /**
     * Get requests pending ICT Officer implementation.
     */
    public function getPendingIctOfficerRequests(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole(['admin', 'ict_officer'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to view these requests.'
                ], 403);
            }

            $requests = $this->getPendingRequestsForStage('ict_officer')
                ->with(['user', 'department'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $requests->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'user_name' => $request->user->name,
                        'pf_number' => $request->pf_number,
                        'department_name' => $request->department->name ?? 'Unknown',
                        'request_type' => $request->request_type,
                        'created_at' => $request->created_at,
                        'updated_at' => $request->updated_at,
                        'workflow_progress' => $request->getWorkflowProgressFromColumns(),
                        'head_it_approved' => !empty($request->head_it_name),
                        'head_it_approved_at' => $request->head_it_approved_at
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching pending ICT Officer requests', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch pending ICT Officer requests.'
            ], 500);
        }
    }

    /**
     * Get implementation statistics.
     */
    public function getImplementationStatistics(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user->hasRole(['admin', 'head_it', 'ict_officer'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to view these statistics.'
                ], 403);
            }

            $stats = [
                'head_it' => [
                    'pending' => $this->getPendingRequestsForStage('head_it')->count(),
                    'approved' => $this->getApprovedRequestsForStage('head_it')->count(),
                ],
                'ict_officer' => [
                    'pending' => $this->getPendingRequestsForStage('ict_officer')->count(),
                    'implemented' => $this->getApprovedRequestsForStage('ict_officer')->count(),
                ],
                'total_implemented' => UserAccess::where('ict_officer_status', 'implemented')->count()
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching implementation statistics', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch implementation statistics.'
            ], 500);
        }
    }
}
