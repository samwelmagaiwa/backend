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
            $query = UserCombinedAccessRequest::with(['user', 'department'])
                ->where('status', 'ict_director_approved')
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
            if ($accessRequest->status !== 'ict_director_approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in the correct status for Head of IT approval'
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
                'head_of_it_approved_at' => now(),
                'head_of_it_approved_by' => Auth::id(),
                'head_of_it_signature_path' => $signaturePath,
                'head_of_it_comments' => 'Approved by Head of IT'
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
            if ($accessRequest->status !== 'ict_director_approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in the correct status for Head of IT rejection'
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
                'head_of_it_rejected_at' => now(),
                'head_of_it_rejected_by' => Auth::id(),
                'head_of_it_signature_path' => $signaturePath,
                'head_of_it_rejection_reason' => $request->reason,
                'head_of_it_comments' => 'Rejected by Head of IT: ' . $request->reason
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
}
