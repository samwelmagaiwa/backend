<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAccess;
use App\Models\IctTaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class IctOfficerController extends Controller
{
    /**
     * Get dashboard data for ICT Officer
     */
    public function getDashboard()
    {
        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Getting dashboard data for ICT Officer', [
                'user_id' => $user->id
            ]);

            // Get task assignment counts
            $assignedTasks = IctTaskAssignment::forIctOfficer($user->id)
                ->withStatus(IctTaskAssignment::STATUS_ASSIGNED)
                ->count();

            $inProgressTasks = IctTaskAssignment::forIctOfficer($user->id)
                ->withStatus(IctTaskAssignment::STATUS_IN_PROGRESS)
                ->count();

            $completedTasks = IctTaskAssignment::forIctOfficer($user->id)
                ->withStatus(IctTaskAssignment::STATUS_COMPLETED)
                ->count();

            $totalTasks = IctTaskAssignment::forIctOfficer($user->id)->count();

            // Get recent assignments (last 10)
            $recentAssignments = IctTaskAssignment::with(['userAccess', 'assignedBy'])
                ->forIctOfficer($user->id)
                ->orderBy('assigned_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'user_access_id' => $assignment->user_access_id,
                        'staff_name' => $assignment->userAccess->staff_name,
                        'pf_number' => $assignment->userAccess->pf_number,
                        'department' => $assignment->userAccess->department->name ?? 'Unknown',
                        'status' => $assignment->status,
                        'status_label' => $assignment->status_label,
                        'assigned_by' => $assignment->assignedBy->name,
                        'assigned_at' => $assignment->assigned_at,
                        'started_at' => $assignment->started_at,
                        'completed_at' => $assignment->completed_at,
                        'assignment_notes' => $assignment->assignment_notes
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data retrieved successfully',
                'data' => [
                    'officer_info' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'department' => $user->department->name ?? 'ICT Department'
                    ],
                    'task_counts' => [
                        'assigned' => $assignedTasks,
                        'in_progress' => $inProgressTasks,
                        'completed' => $completedTasks,
                        'total' => $totalTasks
                    ],
                    'recent_assignments' => $recentAssignments
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('IctOfficerController: Error getting dashboard data', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard data',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get assigned tasks for the ICT Officer
     */
    public function getAssignedTasks(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Getting assigned tasks', [
                'user_id' => $user->id
            ]);

            $status = $request->get('status');
            $query = IctTaskAssignment::with(['userAccess.department', 'assignedBy'])
                ->forIctOfficer($user->id);

            if ($status) {
                $query->withStatus($status);
            }

            $assignments = $query->orderBy('assigned_at', 'desc')
                ->get()
                ->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'user_access_id' => $assignment->user_access_id,
                        'staff_name' => $assignment->userAccess->staff_name,
                        'pf_number' => $assignment->userAccess->pf_number,
                        'phone_number' => $assignment->userAccess->phone_number,
                        'department' => $assignment->userAccess->department->name ?? 'Unknown',
                        'request_types' => $assignment->userAccess->request_type,
                        'internet_purposes' => $assignment->userAccess->internet_purposes,
                        'status' => $assignment->status,
                        'status_label' => $assignment->status_label,
                        'assigned_by' => [
                            'id' => $assignment->assignedBy->id,
                            'name' => $assignment->assignedBy->name,
                            'email' => $assignment->assignedBy->email
                        ],
                        'assigned_at' => $assignment->assigned_at,
                        'started_at' => $assignment->started_at,
                        'completed_at' => $assignment->completed_at,
                        'assignment_notes' => $assignment->assignment_notes,
                        'progress_notes' => $assignment->progress_notes,
                        'completion_notes' => $assignment->completion_notes,
                        'created_at' => $assignment->created_at,
                        'updated_at' => $assignment->updated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Assigned tasks retrieved successfully',
                'data' => [
                    'assignments' => $assignments,
                    'total' => $assignments->count(),
                    'filter_applied' => $status ? "Status: {$status}" : 'All tasks'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('IctOfficerController: Error getting assigned tasks', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve assigned tasks',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Start working on a task
     */
    public function startTask(Request $request, $assignmentId)
    {
        $request->validate([
            'progress_notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Starting task', [
                'user_id' => $user->id,
                'assignment_id' => $assignmentId
            ]);

            $assignment = IctTaskAssignment::forIctOfficer($user->id)
                ->findOrFail($assignmentId);

            // Check if task can be started
            if ($assignment->status !== IctTaskAssignment::STATUS_ASSIGNED) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task cannot be started. Current status: ' . $assignment->status_label
                ], 400);
            }

            // Update assignment to in progress
            $assignment->update([
                'status' => IctTaskAssignment::STATUS_IN_PROGRESS,
                'started_at' => now(),
                'progress_notes' => $request->progress_notes
            ]);

            DB::commit();

            Log::info('IctOfficerController: Task started successfully', [
                'user_id' => $user->id,
                'assignment_id' => $assignmentId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task started successfully',
                'data' => [
                    'assignment_id' => $assignment->id,
                    'status' => $assignment->status,
                    'status_label' => $assignment->status_label,
                    'started_at' => $assignment->started_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('IctOfficerController: Error starting task', [
                'user_id' => Auth::id(),
                'assignment_id' => $assignmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start task',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Complete a task
     */
    public function completeTask(Request $request, $assignmentId)
    {
        $request->validate([
            'completion_notes' => 'required|string|min:10|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Completing task', [
                'user_id' => $user->id,
                'assignment_id' => $assignmentId
            ]);

            $assignment = IctTaskAssignment::with('userAccess')
                ->forIctOfficer($user->id)
                ->findOrFail($assignmentId);

            // Check if task can be completed
            if (!in_array($assignment->status, [IctTaskAssignment::STATUS_ASSIGNED, IctTaskAssignment::STATUS_IN_PROGRESS])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task cannot be completed. Current status: ' . $assignment->status_label
                ], 400);
            }

            // Update assignment to completed
            $assignment->update([
                'status' => IctTaskAssignment::STATUS_COMPLETED,
                'completed_at' => now(),
                'completion_notes' => $request->completion_notes
            ]);

            // Update the UserAccess ICT officer status to implemented
            $assignment->userAccess->update([
                'ict_officer_status' => 'implemented',
                'ict_officer_name' => $user->name,
                'ict_officer_implemented_at' => now(),
                'implementation_comments' => $request->completion_notes
            ]);

            DB::commit();

            Log::info('IctOfficerController: Task completed successfully', [
                'user_id' => $user->id,
                'assignment_id' => $assignmentId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task completed successfully',
                'data' => [
                    'assignment_id' => $assignment->id,
                    'status' => $assignment->status,
                    'status_label' => $assignment->status_label,
                    'completed_at' => $assignment->completed_at,
                    'user_access_status' => $assignment->userAccess->getCalculatedOverallStatus()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('IctOfficerController: Error completing task', [
                'user_id' => Auth::id(),
                'assignment_id' => $assignmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete task',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update task progress
     */
    public function updateTaskProgress(Request $request, $assignmentId)
    {
        $request->validate([
            'progress_notes' => 'required|string|min:5|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Updating task progress', [
                'user_id' => $user->id,
                'assignment_id' => $assignmentId
            ]);

            $assignment = IctTaskAssignment::forIctOfficer($user->id)
                ->findOrFail($assignmentId);

            // Check if task can be updated
            if (!in_array($assignment->status, [IctTaskAssignment::STATUS_ASSIGNED, IctTaskAssignment::STATUS_IN_PROGRESS])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task progress cannot be updated. Current status: ' . $assignment->status_label
                ], 400);
            }

            // Update progress notes and status if not already in progress
            $updateData = [
                'progress_notes' => $request->progress_notes
            ];

            if ($assignment->status === IctTaskAssignment::STATUS_ASSIGNED) {
                $updateData['status'] = IctTaskAssignment::STATUS_IN_PROGRESS;
                $updateData['started_at'] = now();
            }

            $assignment->update($updateData);

            DB::commit();

            Log::info('IctOfficerController: Task progress updated successfully', [
                'user_id' => $user->id,
                'assignment_id' => $assignmentId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task progress updated successfully',
                'data' => [
                    'assignment_id' => $assignment->id,
                    'status' => $assignment->status,
                    'status_label' => $assignment->status_label,
                    'progress_notes' => $assignment->progress_notes,
                    'updated_at' => $assignment->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('IctOfficerController: Error updating task progress', [
                'user_id' => Auth::id(),
                'assignment_id' => $assignmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update task progress',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get task details by ID
     */
    public function getTaskDetails($assignmentId)
    {
        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Getting task details', [
                'user_id' => $user->id,
                'assignment_id' => $assignmentId
            ]);

            $assignment = IctTaskAssignment::with(['userAccess.department', 'assignedBy'])
                ->forIctOfficer($user->id)
                ->findOrFail($assignmentId);

            $taskDetails = [
                'id' => $assignment->id,
                'user_access_id' => $assignment->user_access_id,
                'staff_info' => [
                    'name' => $assignment->userAccess->staff_name,
                    'pf_number' => $assignment->userAccess->pf_number,
                    'phone_number' => $assignment->userAccess->phone_number,
                    'department' => $assignment->userAccess->department->name ?? 'Unknown'
                ],
                'request_details' => [
                    'types' => $assignment->userAccess->request_type,
                    'internet_purposes' => $assignment->userAccess->internet_purposes,
                    'access_type' => $assignment->userAccess->access_type,
                    'temporary_until' => $assignment->userAccess->temporary_until,
                    'wellsoft_modules' => $assignment->userAccess->wellsoft_modules_selected,
                    'jeeva_modules' => $assignment->userAccess->jeeva_modules_selected
                ],
                'assignment_info' => [
                    'status' => $assignment->status,
                    'status_label' => $assignment->status_label,
                    'assigned_by' => [
                        'id' => $assignment->assignedBy->id,
                        'name' => $assignment->assignedBy->name,
                        'email' => $assignment->assignedBy->email
                    ],
                    'assigned_at' => $assignment->assigned_at,
                    'started_at' => $assignment->started_at,
                    'completed_at' => $assignment->completed_at,
                    'assignment_notes' => $assignment->assignment_notes,
                    'progress_notes' => $assignment->progress_notes,
                    'completion_notes' => $assignment->completion_notes
                ],
                'timestamps' => [
                    'created_at' => $assignment->created_at,
                    'updated_at' => $assignment->updated_at
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Task details retrieved successfully',
                'data' => $taskDetails
            ]);

        } catch (\Exception $e) {
            Log::error('IctOfficerController: Error getting task details', [
                'user_id' => Auth::id(),
                'assignment_id' => $assignmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve task details',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get task assignment statistics for the ICT Officer
     */
    public function getTaskStatistics()
    {
        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Getting task statistics', [
                'user_id' => $user->id
            ]);

            $baseQuery = IctTaskAssignment::forIctOfficer($user->id);

            $statistics = [
                'all_time' => [
                    'total' => $baseQuery->count(),
                    'assigned' => (clone $baseQuery)->withStatus(IctTaskAssignment::STATUS_ASSIGNED)->count(),
                    'in_progress' => (clone $baseQuery)->withStatus(IctTaskAssignment::STATUS_IN_PROGRESS)->count(),
                    'completed' => (clone $baseQuery)->withStatus(IctTaskAssignment::STATUS_COMPLETED)->count(),
                    'cancelled' => (clone $baseQuery)->withStatus(IctTaskAssignment::STATUS_CANCELLED)->count(),
                ],
                'this_month' => [
                    'total' => (clone $baseQuery)->whereMonth('assigned_at', now()->month)->count(),
                    'completed' => (clone $baseQuery)->withStatus(IctTaskAssignment::STATUS_COMPLETED)
                        ->whereMonth('completed_at', now()->month)->count(),
                ],
                'this_week' => [
                    'total' => (clone $baseQuery)->whereBetween('assigned_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'completed' => (clone $baseQuery)->withStatus(IctTaskAssignment::STATUS_COMPLETED)
                        ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Task statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('IctOfficerController: Error getting task statistics', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve task statistics',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
