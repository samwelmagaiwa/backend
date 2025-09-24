<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCombinedAccessRequest;
use App\Models\TaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskAssignedNotification;
use Carbon\Carbon;

class HeadOfItController extends Controller
{
    /**
     * Get requests pending Head of IT approval
     */
    public function getPendingRequests(Request $request)
    {
        try {
            Log::info('HeadOfItController: Getting pending requests for Head of IT');
            
            // Get requests that are approved by ICT Director and waiting for Head of IT approval
            // Use granular status columns for more accurate filtering
            $query = UserCombinedAccessRequest::with(['user', 'department'])
                ->where('ict_director_status', 'approved')
                ->where(function($q) {
                    $q->whereNull('head_it_status')
                      ->orWhere('head_it_status', 'pending');
                })
                ->orderBy('ict_director_approved_at', 'asc'); // FIFO ordering

            $requests = $query->get()->map(function ($request) {
                return [
                    'id' => $request->id,
                    'request_id' => $request->request_id,
                    'staff_name' => $request->staff_name,
                    'pf_number' => $request->pf_number,
                    'phone_number' => $request->phone_number,
                    'department' => $request->department_name ?? $request->department->name ?? 'Unknown',
                    'request_types' => is_string($request->request_types) 
                        ? explode(',', $request->request_types) 
                        : $request->request_types,
                    'internet_purposes' => $request->internet_purposes,
                    'status' => $request->status,
                    'created_at' => $request->created_at,
                    'hod_approved_at' => $request->hod_approved_at,
                    'divisional_approved_at' => $request->divisional_approved_at,
                    'ict_director_approved_at' => $request->ict_director_approved_at,
                    'head_of_it_approved_at' => $request->head_of_it_approved_at,
                    'updated_at' => $request->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Requests retrieved successfully',
                'data' => [
                    'requests' => $requests,
                    'total' => $requests->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('HeadOfItController: Error getting pending requests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve requests',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get a specific request by ID
     */
    public function getRequestById($id)
    {
        try {
            Log::info('HeadOfItController: Getting request by ID', ['request_id' => $id]);
            
            $request = UserCombinedAccessRequest::with(['user', 'department'])
                ->findOrFail($id);

            $requestData = [
                'id' => $request->id,
                'request_id' => $request->request_id,
                'staff_name' => $request->staff_name,
                'pf_number' => $request->pf_number,
                'phone_number' => $request->phone_number,
                'department' => $request->department_name ?? $request->department->name ?? 'Unknown',
                'department_name' => $request->department_name ?? $request->department->name ?? 'Unknown',
                'position' => $request->position ?? 'Staff',
                'email' => $request->user->email ?? null,
                'request_types' => is_string($request->request_types) 
                    ? explode(',', $request->request_types) 
                    : $request->request_types,
                'internet_purposes' => $request->internet_purposes,
                'status' => $request->status,
                'created_at' => $request->created_at,
                'hod_approved_at' => $request->hod_approved_at,
                'divisional_approved_at' => $request->divisional_approved_at,
                'ict_director_approved_at' => $request->ict_director_approved_at,
                'head_of_it_approved_at' => $request->head_of_it_approved_at,
                'updated_at' => $request->updated_at
            ];

            return response()->json([
                'success' => true,
                'message' => 'Request retrieved successfully',
                'data' => $requestData
            ]);

        } catch (\Exception $e) {
            Log::error('HeadOfItController: Error getting request by ID', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve request',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Approve a request
     */
    public function approveRequest(Request $request, $id)
    {
        $request->validate([
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:5120' // Max 5MB
        ]);

        DB::beginTransaction();
        
        try {
            Log::info('HeadOfItController: Approving request', ['request_id' => $id]);
            
            $accessRequest = UserCombinedAccessRequest::findOrFail($id);
            
            // Check if request is in correct status for Head of IT approval
            // Use granular status columns for validation
            if ($accessRequest->ict_director_status !== 'approved' || 
                ($accessRequest->head_it_status !== null && $accessRequest->head_it_status !== 'pending')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in the correct status for Head of IT approval',
                    'debug' => [
                        'ict_director_status' => $accessRequest->ict_director_status,
                        'head_it_status' => $accessRequest->head_it_status,
                        'overall_status' => $accessRequest->status
                    ]
                ], 400);
            }

            // Handle signature upload
            $signaturePath = null;
            if ($request->hasFile('signature')) {
                $signatureFile = $request->file('signature');
                $fileName = 'head_of_it_signature_' . $id . '_' . time() . '.' . $signatureFile->extension();
                $signaturePath = $signatureFile->storeAs('signatures/head_of_it', $fileName, 'public');
            }

            // Update request status and approval information
            $accessRequest->update([
                'status' => 'head_of_it_approved',
                'head_it_status' => 'approved',
                'head_it_approved_at' => now(),
                'head_it_approved_by' => Auth::id(),
                'head_it_signature_path' => $signaturePath,
                'head_it_comments' => 'Approved by Head of IT'
            ]);

            DB::commit();

            Log::info('HeadOfItController: Request approved successfully', ['request_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Request approved successfully',
                'data' => [
                    'request_id' => $accessRequest->id,
                    'status' => $accessRequest->status,
                    'approved_at' => $accessRequest->head_of_it_approved_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('HeadOfItController: Error approving request', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve request',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Reject a request
     */
    public function rejectRequest(Request $request, $id)
    {
        $request->validate([
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:5120', // Max 5MB
            'reason' => 'required|string|min:10|max:1000'
        ]);

        DB::beginTransaction();
        
        try {
            Log::info('HeadOfItController: Rejecting request', ['request_id' => $id]);
            
            $accessRequest = UserCombinedAccessRequest::findOrFail($id);
            
            // Check if request is in correct status for Head of IT rejection
            // Use granular status columns for validation
            if ($accessRequest->ict_director_status !== 'approved' || 
                ($accessRequest->head_it_status !== null && $accessRequest->head_it_status !== 'pending')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in the correct status for Head of IT rejection',
                    'debug' => [
                        'ict_director_status' => $accessRequest->ict_director_status,
                        'head_it_status' => $accessRequest->head_it_status,
                        'overall_status' => $accessRequest->status
                    ]
                ], 400);
            }

            // Handle signature upload
            $signaturePath = null;
            if ($request->hasFile('signature')) {
                $signatureFile = $request->file('signature');
                $fileName = 'head_of_it_rejection_signature_' . $id . '_' . time() . '.' . $signatureFile->extension();
                $signaturePath = $signatureFile->storeAs('signatures/head_of_it', $fileName, 'public');
            }

            // Update request status and rejection information
            $accessRequest->update([
                'status' => 'head_of_it_rejected',
                'head_it_status' => 'rejected',
                'head_it_rejected_at' => now(),
                'head_it_rejected_by' => Auth::id(),
                'head_it_signature_path' => $signaturePath,
                'head_it_rejection_reason' => $request->reason,
                'head_it_comments' => 'Rejected by Head of IT: ' . $request->reason
            ]);

            DB::commit();

            Log::info('HeadOfItController: Request rejected successfully', ['request_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Request rejected successfully',
                'data' => [
                    'request_id' => $accessRequest->id,
                    'status' => $accessRequest->status,
                    'rejected_at' => $accessRequest->head_of_it_rejected_at,
                    'reason' => $request->reason
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('HeadOfItController: Error rejecting request', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject request',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get list of available ICT Officers
     */
    public function getIctOfficers()
    {
        try {
            Log::info('HeadOfItController: Getting ICT officers list');
            
            // Get users with ICT Officer role
            $ictOfficers = User::role('ict_officer')
                ->with(['department', 'currentTaskAssignments'])
                ->where('is_active', true)
                ->get()
                ->map(function ($officer) {
                    // Determine officer status based on current assignments
                    $activeAssignments = $officer->currentTaskAssignments()
                        ->whereIn('status', ['assigned', 'in_progress'])
                        ->count();
                    
                    $status = 'Available';
                    if ($activeAssignments > 0) {
                        $status = $activeAssignments >= 3 ? 'Busy' : 'Assigned';
                    }

                    return [
                        'id' => $officer->id,
                        'name' => $officer->name,
                        'pf_number' => $officer->pf_number,
                        'phone_number' => $officer->phone,
                        'email' => $officer->email,
                        'department' => $officer->department->name ?? 'ICT Department',
                        'position' => 'ICT Officer',
                        'status' => $status,
                        'active_assignments' => $activeAssignments,
                        'is_active' => $officer->is_active,
                        'created_at' => $officer->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'ICT officers retrieved successfully',
                'data' => $ictOfficers
            ]);

        } catch (\Exception $e) {
            Log::error('HeadOfItController: Error getting ICT officers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ICT officers',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Assign a task to an ICT Officer
     */
    public function assignTaskToIctOfficer(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:user_combined_access_requests,id',
            'ict_officer_id' => 'required|exists:users,id'
        ]);

        DB::beginTransaction();
        
        try {
            Log::info('HeadOfItController: Assigning task to ICT officer', [
                'request_id' => $request->request_id,
                'ict_officer_id' => $request->ict_officer_id
            ]);
            
            $accessRequest = UserCombinedAccessRequest::findOrFail($request->request_id);
            $ictOfficer = User::findOrFail($request->ict_officer_id);
            
            // Verify ICT Officer role
            if (!$ictOfficer->hasRole('ict_officer')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user is not an ICT Officer'
                ], 400);
            }

            // Check if request is approved by Head of IT
            if ($accessRequest->status !== 'head_of_it_approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be approved by Head of IT before assignment'
                ], 400);
            }

            // Check if task is already assigned
            $existingAssignment = TaskAssignment::where('request_id', $request->request_id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->first();

            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task is already assigned to another ICT Officer'
                ], 400);
            }

            // Create task assignment
            $taskAssignment = TaskAssignment::create([
                'request_id' => $request->request_id,
                'assigned_to' => $request->ict_officer_id,
                'assigned_by' => Auth::id(),
                'status' => 'assigned',
                'assigned_at' => now(),
                'priority' => 'normal',
                'description' => 'Release access for ' . $accessRequest->staff_name,
                'estimated_completion' => now()->addDays(3), // Default 3 days
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update request status
            $accessRequest->update([
                'status' => 'assigned_to_ict',
                'assigned_ict_officer_id' => $request->ict_officer_id,
                'task_assigned_at' => now()
            ]);

            // Send notification to ICT Officer
            $ictOfficer->notify(new TaskAssignedNotification($accessRequest, $taskAssignment));

            DB::commit();

            Log::info('HeadOfItController: Task assigned successfully', [
                'request_id' => $request->request_id,
                'ict_officer_id' => $request->ict_officer_id,
                'assignment_id' => $taskAssignment->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task assigned successfully to ' . $ictOfficer->name,
                'data' => [
                    'assignment_id' => $taskAssignment->id,
                    'request_id' => $accessRequest->id,
                    'ict_officer' => [
                        'id' => $ictOfficer->id,
                        'name' => $ictOfficer->name,
                        'email' => $ictOfficer->email
                    ],
                    'status' => $accessRequest->status,
                    'assigned_at' => $taskAssignment->assigned_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('HeadOfItController: Error assigning task', [
                'request_id' => $request->request_id,
                'ict_officer_id' => $request->ict_officer_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign task',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get task assignment history for a request
     */
    public function getTaskHistory($requestId)
    {
        try {
            Log::info('HeadOfItController: Getting task history', ['request_id' => $requestId]);
            
            $assignments = TaskAssignment::with(['assignedTo', 'assignedBy'])
                ->where('request_id', $requestId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'status' => $assignment->status,
                        'assigned_to' => [
                            'id' => $assignment->assignedTo->id,
                            'name' => $assignment->assignedTo->name,
                            'email' => $assignment->assignedTo->email
                        ],
                        'assigned_by' => [
                            'id' => $assignment->assignedBy->id,
                            'name' => $assignment->assignedBy->name,
                            'email' => $assignment->assignedBy->email
                        ],
                        'assigned_at' => $assignment->assigned_at,
                        'completed_at' => $assignment->completed_at,
                        'description' => $assignment->description,
                        'priority' => $assignment->priority,
                        'progress_notes' => $assignment->progress_notes,
                        'estimated_completion' => $assignment->estimated_completion
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Task history retrieved successfully',
                'data' => $assignments
            ]);

        } catch (\Exception $e) {
            Log::error('HeadOfItController: Error getting task history', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve task history',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Cancel task assignment
     */
    public function cancelTaskAssignment($requestId)
    {
        DB::beginTransaction();
        
        try {
            Log::info('HeadOfItController: Canceling task assignment', ['request_id' => $requestId]);
            
            $accessRequest = UserCombinedAccessRequest::findOrFail($requestId);
            
            // Find active assignment
            $assignment = TaskAssignment::where('request_id', $requestId)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->first();

            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active assignment found for this request'
                ], 404);
            }

            // Update assignment status
            $assignment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id(),
                'cancellation_reason' => 'Cancelled by Head of IT'
            ]);

            // Update request status back to head_of_it_approved
            $accessRequest->update([
                'status' => 'head_of_it_approved',
                'assigned_ict_officer_id' => null,
                'task_assigned_at' => null
            ]);

            DB::commit();

            Log::info('HeadOfItController: Task assignment cancelled successfully', ['request_id' => $requestId]);

            return response()->json([
                'success' => true,
                'message' => 'Task assignment cancelled successfully',
                'data' => [
                    'request_id' => $accessRequest->id,
                    'status' => $accessRequest->status,
                    'cancelled_at' => $assignment->cancelled_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('HeadOfItController: Error canceling task assignment', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel task assignment',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    // ========================================
    // NEW ICT TASK ASSIGNMENT METHODS
    // ========================================

    /**
     * Get requests pending Head of IT approval (NEW UserAccess system)
     */
    public function getPendingIctRequests(Request $request)
    {
        try {
            Log::info('HeadOfItController: Getting pending ICT requests for Head of IT');
            
            // Get requests that are approved by ICT Director and waiting for Head of IT approval
            $query = \App\Models\UserAccess::with(['user', 'department'])
                ->where('ict_director_status', 'approved')
                ->where(function($q) {
                    $q->whereNull('head_it_status')
                      ->orWhere('head_it_status', 'pending');
                })
                ->orderBy('ict_director_approved_at', 'asc'); // FIFO ordering

            $requests = $query->get()->map(function ($accessRequest) {
                return [
                    'id' => $accessRequest->id,
                    'staff_name' => $accessRequest->staff_name,
                    'pf_number' => $accessRequest->pf_number,
                    'phone_number' => $accessRequest->phone_number,
                    'department' => $accessRequest->department->name ?? 'Unknown',
                    'request_types' => is_string($accessRequest->request_type) 
                        ? explode(',', $accessRequest->request_type) 
                        : $accessRequest->request_type,
                    'internet_purposes' => $accessRequest->internet_purposes,
                    'status' => $accessRequest->getCalculatedOverallStatus(),
                    'created_at' => $accessRequest->created_at,
                    'hod_approved_at' => $accessRequest->hod_approved_at,
                    'divisional_approved_at' => $accessRequest->divisional_approved_at,
                    'ict_director_approved_at' => $accessRequest->ict_director_approved_at,
                    'head_it_approved_at' => $accessRequest->head_it_approved_at,
                    'updated_at' => $accessRequest->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'ICT requests retrieved successfully',
                'data' => [
                    'requests' => $requests,
                    'total' => $requests->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('HeadOfItController: Error getting pending ICT requests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ICT requests',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get available ICT Officers for task assignment (NEW system)
     */
    public function getAvailableIctOfficers()
    {
        try {
            Log::info('HeadOfItController: Getting available ICT officers list');
            
            // Get users with ICT Officer role
            $ictOfficers = User::whereHas('roles', function($q) {
                    $q->where('name', 'ict_officer');
                })
                ->with(['department'])
                ->where('is_active', true)
                ->get()
                ->map(function ($officer) {
                    // Get current ICT task workload
                    $activeAssignments = $officer->getCurrentIctWorkload();
                    $availabilityStatus = $officer->getIctTaskAvailabilityStatus();
                    
                    return [
                        'id' => $officer->id,
                        'name' => $officer->name,
                        'pf_number' => $officer->pf_number,
                        'phone_number' => $officer->phone,
                        'email' => $officer->email,
                        'department' => $officer->department->name ?? 'ICT Department',
                        'position' => 'ICT Officer',
                        'availability_status' => $availabilityStatus,
                        'active_assignments' => $activeAssignments,
                        'is_active' => $officer->is_active,
                        'is_available_for_tasks' => $officer->isAvailableForIctTasks(),
                        'created_at' => $officer->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'ICT officers retrieved successfully',
                'data' => $ictOfficers
            ]);

        } catch (\Exception $e) {
            Log::error('HeadOfItController: Error getting available ICT officers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ICT officers',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Assign ICT task to officer (NEW system)
     */
    public function assignIctTask(Request $request)
    {
        $request->validate([
            'user_access_id' => 'required|exists:user_access,id',
            'ict_officer_user_id' => 'required|exists:users,id',
            'assignment_notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        
        try {
            Log::info('HeadOfItController: Assigning ICT task to officer', [
                'user_access_id' => $request->user_access_id,
                'ict_officer_user_id' => $request->ict_officer_user_id
            ]);
            
            $userAccess = \App\Models\UserAccess::findOrFail($request->user_access_id);
            $ictOfficer = User::findOrFail($request->ict_officer_user_id);
            
            // Verify ICT Officer role
            if (!$ictOfficer->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user is not an ICT Officer'
                ], 400);
            }

            // Check if request is approved by Head of IT
            if ($userAccess->head_it_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be approved by Head of IT before assignment',
                    'debug' => [
                        'head_it_status' => $userAccess->head_it_status,
                        'calculated_status' => $userAccess->getCalculatedOverallStatus()
                    ]
                ], 400);
            }

            // Check if task is already assigned
            $existingAssignment = \App\Models\IctTaskAssignment::where('user_access_id', $request->user_access_id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->first();

            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task is already assigned to another ICT Officer'
                ], 400);
            }

            // Create ICT task assignment
            $ictTaskAssignment = \App\Models\IctTaskAssignment::create([
                'user_access_id' => $request->user_access_id,
                'ict_officer_user_id' => $request->ict_officer_user_id,
                'assigned_by_user_id' => Auth::id(),
                'status' => \App\Models\IctTaskAssignment::STATUS_ASSIGNED,
                'assignment_notes' => $request->assignment_notes ?? 'ICT task assigned by Head of IT',
                'assigned_at' => now()
            ]);

            // Update user access ICT Officer status to pending
            $userAccess->update([
                'ict_officer_status' => 'pending'
            ]);

            // Send notification to ICT Officer (if notification class exists)
            try {
                $ictOfficer->notify(new \App\Notifications\IctTaskAssignedNotification($userAccess, $ictTaskAssignment));
            } catch (\Exception $notificationError) {
                Log::warning('Failed to send ICT task notification', [
                    'error' => $notificationError->getMessage()
                ]);
            }

            DB::commit();

            Log::info('HeadOfItController: ICT task assigned successfully', [
                'user_access_id' => $request->user_access_id,
                'ict_officer_user_id' => $request->ict_officer_user_id,
                'assignment_id' => $ictTaskAssignment->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'ICT task assigned successfully to ' . $ictOfficer->name,
                'data' => [
                    'assignment_id' => $ictTaskAssignment->id,
                    'user_access_id' => $userAccess->id,
                    'ict_officer' => [
                        'id' => $ictOfficer->id,
                        'name' => $ictOfficer->name,
                        'email' => $ictOfficer->email
                    ],
                    'status' => $userAccess->getCalculatedOverallStatus(),
                    'assigned_at' => $ictTaskAssignment->assigned_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('HeadOfItController: Error assigning ICT task', [
                'user_access_id' => $request->user_access_id,
                'ict_officer_user_id' => $request->ict_officer_user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign ICT task',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get ICT task assignment history for a request (NEW system)
     */
    public function getIctTaskHistory($userAccessId)
    {
        try {
            Log::info('HeadOfItController: Getting ICT task history', ['user_access_id' => $userAccessId]);
            
            $assignments = \App\Models\IctTaskAssignment::with(['ictOfficer', 'assignedBy'])
                ->where('user_access_id', $userAccessId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'status' => $assignment->status,
                        'status_label' => $assignment->status_label,
                        'ict_officer' => [
                            'id' => $assignment->ictOfficer->id,
                            'name' => $assignment->ictOfficer->name,
                            'email' => $assignment->ictOfficer->email
                        ],
                        'assigned_by' => [
                            'id' => $assignment->assignedBy->id,
                            'name' => $assignment->assignedBy->name,
                            'email' => $assignment->assignedBy->email
                        ],
                        'assigned_at' => $assignment->assigned_at,
                        'started_at' => $assignment->started_at,
                        'completed_at' => $assignment->completed_at,
                        'cancelled_at' => $assignment->cancelled_at,
                        'assignment_notes' => $assignment->assignment_notes,
                        'progress_notes' => $assignment->progress_notes,
                        'completion_notes' => $assignment->completion_notes,
                        'created_at' => $assignment->created_at,
                        'updated_at' => $assignment->updated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'ICT task history retrieved successfully',
                'data' => $assignments
            ]);

        } catch (\Exception $e) {
            Log::error('HeadOfItController: Error getting ICT task history', [
                'user_access_id' => $userAccessId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ICT task history',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Cancel ICT task assignment (NEW system)
     */
    public function cancelIctTaskAssignment($userAccessId)
    {
        DB::beginTransaction();
        
        try {
            Log::info('HeadOfItController: Canceling ICT task assignment', ['user_access_id' => $userAccessId]);
            
            $userAccess = \App\Models\UserAccess::findOrFail($userAccessId);
            
            // Find active ICT assignment
            $assignment = \App\Models\IctTaskAssignment::where('user_access_id', $userAccessId)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->first();

            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active ICT assignment found for this request'
                ], 404);
            }

            // Update assignment status
            $assignment->update([
                'status' => \App\Models\IctTaskAssignment::STATUS_CANCELLED,
                'cancelled_at' => now()
            ]);

            // Update user access ICT Officer status back to pending
            $userAccess->update([
                'ict_officer_status' => 'pending'
            ]);

            DB::commit();

            Log::info('HeadOfItController: ICT task assignment cancelled successfully', ['user_access_id' => $userAccessId]);

            return response()->json([
                'success' => true,
                'message' => 'ICT task assignment cancelled successfully',
                'data' => [
                    'user_access_id' => $userAccess->id,
                    'status' => $userAccess->getCalculatedOverallStatus(),
                    'cancelled_at' => $assignment->cancelled_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('HeadOfItController: Error canceling ICT task assignment', [
                'user_access_id' => $userAccessId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel ICT task assignment',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
