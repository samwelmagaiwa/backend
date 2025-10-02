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

    /**
     * Get all access requests approved by Head of IT (available for ICT Officer implementation)
     */
    public function getAccessRequests(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Getting access requests for implementation', [
                'user_id' => $user->id
            ]);

            // Get all requests that are approved by Head of IT and ready for ICT Officer implementation
            $query = UserAccess::with(['department', 'ictTaskAssignments.ictOfficer'])
                ->where('head_it_status', 'approved')
                ->whereNotNull('head_it_approved_at')
                ->orderBy('head_it_approved_at', 'asc'); // FIFO - First approved, first implemented

            // Apply status filter if provided
            $status = $request->get('status');
            if ($status) {
                if ($status === 'unassigned') {
                    // Requests that don't have any ICT task assignments
                    $query->whereDoesntHave('ictTaskAssignments');
                } elseif ($status === 'assigned_to_ict') {
                    // Requests that have ICT task assignments with 'assigned' status
                    $query->whereHas('ictTaskAssignments', function ($q) {
                        $q->where('status', IctTaskAssignment::STATUS_ASSIGNED);
                    });
                } elseif ($status === 'implementation_in_progress') {
                    // Requests that have ICT task assignments with 'in_progress' status
                    $query->whereHas('ictTaskAssignments', function ($q) {
                        $q->where('status', IctTaskAssignment::STATUS_IN_PROGRESS);
                    });
                } elseif ($status === 'completed') {
                    // Requests that have ICT task assignments with 'completed' status
                    $query->whereHas('ictTaskAssignments', function ($q) {
                        $q->where('status', IctTaskAssignment::STATUS_COMPLETED);
                    });
                }
            }

            $accessRequests = $query->get()->map(function ($request) {
                // Determine current status based on ICT task assignments
                $currentStatus = 'head_of_it_approved'; // Default status
                $latestAssignment = $request->ictTaskAssignments->sortByDesc('assigned_at')->first();
                
                if ($latestAssignment) {
                    switch ($latestAssignment->status) {
                        case IctTaskAssignment::STATUS_ASSIGNED:
                            $currentStatus = 'assigned_to_ict';
                            break;
                        case IctTaskAssignment::STATUS_IN_PROGRESS:
                            $currentStatus = 'implementation_in_progress';
                            break;
                        case IctTaskAssignment::STATUS_COMPLETED:
                            $currentStatus = 'completed';
                            break;
                        case IctTaskAssignment::STATUS_CANCELLED:
                            $currentStatus = 'head_of_it_approved'; // Back to available for assignment
                            break;
                    }
                }

                // Determine service flags from request_type array
                $requestTypes = is_array($request->request_type) ? $request->request_type : [];
                $jeevaRequired = in_array('jeeva_access', $requestTypes) || !empty($request->jeeva_modules_selected);
                $wellsoftRequired = in_array('wellsoft', $requestTypes) || !empty($request->wellsoft_modules_selected);
                $internetRequired = in_array('internet_access_request', $requestTypes) || !empty($request->internet_purposes);
                
                return [
                    'id' => $request->id,
                    'request_id' => "REQ-" . str_pad($request->id, 6, '0', STR_PAD_LEFT),
                    'staff_name' => $request->staff_name,
                    'pf_number' => $request->pf_number,
                    'phone_number' => $request->phone_number,
                    'department_name' => $request->department->name ?? 'Unknown Department',
                    'department_id' => $request->department_id,
                    
                    // Service flags (derived from request_type and modules)
                    'jeeva_access_required' => $jeevaRequired,
                    'wellsoft_access_required' => $wellsoftRequired,
                    'internet_access_required' => $internetRequired,
                    
                    // Request details
                    'request_types' => $request->request_type,
                    'access_type' => $request->access_type,
                    'internet_purposes' => $request->internet_purposes,
                    'wellsoft_modules_selected' => $request->wellsoft_modules_selected,
                    'jeeva_modules_selected' => $request->jeeva_modules_selected,
                    
                    // Status and dates
                    'status' => $currentStatus,
                    'head_of_it_approval_date' => $request->head_it_approved_at,
                    'head_of_it_approved_at' => $request->head_it_approved_at,
                    
                    // ICT task assignment info
                    'ict_task_assignments' => $request->ictTaskAssignments->map(function ($assignment) {
                        return [
                            'id' => $assignment->id,
                            'ict_officer_id' => $assignment->ict_officer_user_id,
                            'ict_officer_name' => $assignment->ictOfficer->name ?? 'Unknown Officer',
                            'status' => $assignment->status,
                            'status_label' => $assignment->status_label,
                            'assigned_at' => $assignment->assigned_at,
                            'started_at' => $assignment->started_at,
                            'completed_at' => $assignment->completed_at,
                            'assignment_notes' => $assignment->assignment_notes
                        ];
                    }),
                    
                    'created_at' => $request->created_at,
                    'updated_at' => $request->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Access requests retrieved successfully',
                'data' => $accessRequests,
                'meta' => [
                    'total' => $accessRequests->count(),
                    'filter_applied' => $status ? "Status: {$status}" : 'All requests'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('IctOfficerController: Error getting access requests', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve access requests',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Assign an access request task to self (ICT Officer takes ownership)
     */
    public function assignAccessRequestToSelf(Request $request, $requestId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
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

            Log::info('IctOfficerController: Assigning access request to self', [
                'user_id' => $user->id,
                'request_id' => $requestId
            ]);

            // Find the access request
            $userAccess = UserAccess::where('id', $requestId)
                ->where('head_it_status', 'approved')
                ->whereNotNull('head_it_approved_at')
                ->firstOrFail();

            // Check if already assigned
            $existingAssignment = IctTaskAssignment::where('user_access_id', $requestId)
                ->whereIn('status', [IctTaskAssignment::STATUS_ASSIGNED, IctTaskAssignment::STATUS_IN_PROGRESS])
                ->first();

            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'This request is already assigned to an ICT Officer'
                ], 400);
            }

            // Create new ICT task assignment
            $assignment = IctTaskAssignment::create([
                'user_access_id' => $requestId,
                'ict_officer_user_id' => $user->id,
                'assigned_by_user_id' => $user->id, // Self-assigned
                'status' => IctTaskAssignment::STATUS_ASSIGNED,
                'assigned_at' => now(),
                'assignment_notes' => $request->notes ?? 'Task self-assigned by ICT Officer'
            ]);

            // Update user access ICT officer status
            $userAccess->update([
                'ict_officer_status' => 'assigned',
                'ict_officer_user_id' => $user->id,
                'ict_officer_assigned_at' => now()
            ]);

            DB::commit();

            Log::info('IctOfficerController: Access request assigned to self successfully', [
                'user_id' => $user->id,
                'request_id' => $requestId,
                'assignment_id' => $assignment->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Access request assigned successfully',
                'data' => [
                    'assignment_id' => $assignment->id,
                    'request_id' => $requestId,
                    'status' => $assignment->status,
                    'status_label' => $assignment->status_label,
                    'assigned_at' => $assignment->assigned_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('IctOfficerController: Error assigning access request to self', [
                'user_id' => Auth::id(),
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign access request',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update progress on an assigned access request
     */
    public function updateAccessRequestProgress(Request $request, $requestId)
    {
        try {
            $user = Auth::user();
            
            // Debug: Log initial request details
            Log::info('IctOfficerController: Starting progress update', [
                'user_id' => $user ? $user->id : null,
                'user_email' => $user ? $user->email : null,
                'user_roles' => $user ? $user->roles->pluck('name') : null,
                'request_id' => $requestId,
                'request_payload' => $request->all(),
                'user_is_ict_officer' => $user ? $user->isIctOfficer() : false
            ]);
            
            if (!$user) {
                Log::error('IctOfficerController: No authenticated user found');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            if (!$user->isIctOfficer()) {
                Log::error('IctOfficerController: User is not ICT Officer', [
                    'user_id' => $user->id,
                    'user_roles' => $user->roles->pluck('name')
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }
            
            // Validate request data
            $request->validate([
                'status' => 'required|in:implementation_in_progress,completed',
                'notes' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();

            Log::info('IctOfficerController: Updating access request progress', [
                'user_id' => $user->id,
                'request_id' => $requestId,
                'new_status' => $request->status
            ]);

            // Check if UserAccess exists first
            $userAccess = UserAccess::find($requestId);
            if (!$userAccess) {
                Log::error('IctOfficerController: UserAccess not found', [
                    'request_id' => $requestId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Access request not found'
                ], 404);
            }
            
            Log::info('IctOfficerController: UserAccess found', [
                'user_access_id' => $userAccess->id,
                'staff_name' => $userAccess->staff_name,
                'head_it_status' => $userAccess->head_it_status,
                'ict_officer_status' => $userAccess->ict_officer_status
            ]);

            // Find the assignment
            $assignment = IctTaskAssignment::where('user_access_id', $requestId)
                ->where('ict_officer_user_id', $user->id)
                ->whereIn('status', [IctTaskAssignment::STATUS_ASSIGNED, IctTaskAssignment::STATUS_IN_PROGRESS])
                ->first();
                
            if (!$assignment) {
                Log::error('IctOfficerController: No valid assignment found', [
                    'user_id' => $user->id,
                    'request_id' => $requestId,
                    'all_assignments' => IctTaskAssignment::where('user_access_id', $requestId)->get()->toArray()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No valid assignment found for this request. You may need to assign this task to yourself first.'
                ], 400);
            }

            $updateData = [];
            $userAccessUpdate = [];

            if ($request->status === 'implementation_in_progress') {
                $updateData['status'] = IctTaskAssignment::STATUS_IN_PROGRESS;
                $updateData['progress_notes'] = $request->notes;
                $userAccessUpdate['ict_officer_status'] = 'in_progress';
                
                if ($assignment->status === IctTaskAssignment::STATUS_ASSIGNED) {
                    $updateData['started_at'] = now();
                    $userAccessUpdate['ict_officer_started_at'] = now();
                }
                
            } elseif ($request->status === 'completed') {
                $updateData['status'] = IctTaskAssignment::STATUS_COMPLETED;
                $updateData['completion_notes'] = $request->notes;
                $updateData['completed_at'] = now();
                $userAccessUpdate['ict_officer_status'] = 'implemented';
                $userAccessUpdate['ict_officer_implemented_at'] = now();
                $userAccessUpdate['implementation_comments'] = $request->notes;
                
                if (!$assignment->started_at) {
                    $updateData['started_at'] = now();
                    $userAccessUpdate['ict_officer_started_at'] = now();
                }
            }

            // Update assignment
            $assignment->update($updateData);

            // Update user access record
            $assignment->userAccess->update($userAccessUpdate);

            DB::commit();

            Log::info('IctOfficerController: Access request progress updated successfully', [
                'user_id' => $user->id,
                'request_id' => $requestId,
                'assignment_id' => $assignment->id,
                'new_status' => $assignment->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Access request progress updated successfully',
                'data' => [
                    'assignment_id' => $assignment->id,
                    'request_id' => $requestId,
                    'status' => $assignment->status,
                    'status_label' => $assignment->status_label,
                    'updated_at' => $assignment->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('IctOfficerController: Error updating access request progress', [
                'user_id' => Auth::id(),
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update access request progress',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Cancel an assigned access request task
     */
    public function cancelAccessRequestTask(Request $request, $requestId)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500'
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

            Log::info('IctOfficerController: Canceling access request task', [
                'user_id' => $user->id,
                'request_id' => $requestId
            ]);

            // Find the assignment
            $assignment = IctTaskAssignment::where('user_access_id', $requestId)
                ->where('ict_officer_user_id', $user->id)
                ->whereIn('status', [IctTaskAssignment::STATUS_ASSIGNED, IctTaskAssignment::STATUS_IN_PROGRESS])
                ->firstOrFail();

            // Update assignment to cancelled
            $assignment->update([
                'status' => IctTaskAssignment::STATUS_CANCELLED,
                'completion_notes' => 'Task cancelled by ICT Officer. Reason: ' . $request->reason,
                'completed_at' => now()
            ]);

            // Reset user access ICT officer status to make it available for reassignment
            $assignment->userAccess->update([
                'ict_officer_status' => null,
                'ict_officer_user_id' => null,
                'ict_officer_assigned_at' => null,
                'ict_officer_started_at' => null
            ]);

            DB::commit();

            Log::info('IctOfficerController: Access request task canceled successfully', [
                'user_id' => $user->id,
                'request_id' => $requestId,
                'assignment_id' => $assignment->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Access request task canceled successfully',
                'data' => [
                    'assignment_id' => $assignment->id,
                    'request_id' => $requestId,
                    'status' => $assignment->status,
                    'status_label' => $assignment->status_label,
                    'canceled_at' => $assignment->completed_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('IctOfficerController: Error canceling access request task', [
                'user_id' => Auth::id(),
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel access request task',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get a specific access request by ID
     */
    public function getAccessRequestById($requestId)
    {
        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Getting access request by ID', [
                'user_id' => $user->id,
                'request_id' => $requestId
            ]);

            // Find the access request
            $request = UserAccess::with(['department', 'ictTaskAssignments.ictOfficer'])
                ->where('id', $requestId)
                ->where('head_it_status', 'approved')
                ->whereNotNull('head_it_approved_at')
                ->first();

            if (!$request) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access request not found or not approved by Head of IT'
                ], 404);
            }

            // Determine current status based on ICT task assignments
            $currentStatus = 'head_of_it_approved'; // Default status
            $latestAssignment = $request->ictTaskAssignments->sortByDesc('assigned_at')->first();
            
            if ($latestAssignment) {
                switch ($latestAssignment->status) {
                    case IctTaskAssignment::STATUS_ASSIGNED:
                        $currentStatus = 'assigned_to_ict';
                        break;
                    case IctTaskAssignment::STATUS_IN_PROGRESS:
                        $currentStatus = 'implementation_in_progress';
                        break;
                    case IctTaskAssignment::STATUS_COMPLETED:
                        $currentStatus = 'completed';
                        break;
                    case IctTaskAssignment::STATUS_CANCELLED:
                        $currentStatus = 'head_of_it_approved'; // Back to available for assignment
                        break;
                }
            }

            // Determine service flags from request_type array
            $requestTypes = is_array($request->request_type) ? $request->request_type : [];
            $jeevaRequired = in_array('jeeva_access', $requestTypes) || !empty($request->jeeva_modules_selected);
            $wellsoftRequired = in_array('wellsoft', $requestTypes) || !empty($request->wellsoft_modules_selected);
            $internetRequired = in_array('internet_access_request', $requestTypes) || !empty($request->internet_purposes);
            
            $requestData = [
                'id' => $request->id,
                'request_id' => "REQ-" . str_pad($request->id, 6, '0', STR_PAD_LEFT),
                'staff_name' => $request->staff_name,
                'pf_number' => $request->pf_number,
                'phone_number' => $request->phone_number,
                'department_name' => $request->department->name ?? 'Unknown Department',
                'department_id' => $request->department_id,
                
                // Service flags (derived from request_type and modules)
                'jeeva_access_required' => $jeevaRequired,
                'wellsoft_access_required' => $wellsoftRequired,
                'internet_access_required' => $internetRequired,
                
                // Request details
                'request_types' => $request->request_type,
                'access_type' => $request->access_type,
                'internet_purposes' => $request->internet_purposes,
                'wellsoft_modules_selected' => $request->wellsoft_modules_selected,
                'jeeva_modules_selected' => $request->jeeva_modules_selected,
                
                // Status and dates
                'status' => $currentStatus,
                'head_of_it_approval_date' => $request->head_it_approved_at,
                'head_of_it_approved_at' => $request->head_it_approved_at,
                
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at
            ];

            return response()->json([
                'success' => true,
                'message' => 'Access request retrieved successfully',
                'data' => $requestData
            ]);

        } catch (\Exception $e) {
            Log::error('IctOfficerController: Error getting access request by ID', [
                'user_id' => Auth::id(),
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve access request',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get detailed timeline for a specific access request
     */
    public function getAccessRequestTimeline($requestId)
    {
        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Getting access request timeline', [
                'user_id' => $user->id,
                'request_id' => $requestId
            ]);

            // Find the access request with all related data
            // Allow access to requests that have reached ICT Director stage (pending or approved by Head of IT)
            $request = UserAccess::with([
                'department',
                'user',
                'ictTaskAssignments.ictOfficer',
                'ictTaskAssignments.assignedBy'
            ])
            ->where('id', $requestId)
            ->where(function ($query) {
                $query->where('ict_director_status', 'approved') // Must be approved by ICT Director
                      ->whereNotNull('ict_director_approved_at');
            })
            ->firstOrFail();

            // Build comprehensive request data for timeline
            $timelineData = [
                'request' => [
                    'id' => $request->id,
                    'staff_name' => $request->staff_name,
                    'pf_number' => $request->pf_number,
                    'phone_number' => $request->phone_number,
                    'department' => [
                        'id' => $request->department->id ?? null,
                        'name' => $request->department->name ?? 'Unknown Department'
                    ],
                    'signature_path' => $request->signature_path,
                    
                    // Request details
                    'request_type' => $request->request_type,
                    'request_type_name' => $request->request_type_name,
                    'access_type' => $request->access_type,
                    'access_type_name' => $request->access_type_name,
                    'temporary_until' => $request->temporary_until,
                    'internet_purposes' => $request->internet_purposes,
                    'wellsoft_modules_selected' => $request->wellsoft_modules_selected,
                    'jeeva_modules_selected' => $request->jeeva_modules_selected,
                    
                    // HOD approval stage
                    'hod_status' => $request->hod_status,
                    'hod_name' => $request->hod_name,
                    'hod_comments' => $request->hod_comments,
                    'hod_signature_path' => $request->hod_signature_path,
                    'hod_approved_at' => $request->hod_approved_at,
                    'hod_approved_by' => $request->hod_approved_by,
                    'hod_approved_by_name' => $request->hod_approved_by_name,
                    
                    // Divisional Director approval stage
                    'divisional_status' => $request->divisional_status,
                    'divisional_director_name' => $request->divisional_director_name,
                    'divisional_director_comments' => $request->divisional_director_comments,
                    'divisional_director_signature_path' => $request->divisional_director_signature_path,
                    'divisional_approved_at' => $request->divisional_approved_at,
                    
                    // ICT Director approval stage
                    'ict_director_status' => $request->ict_director_status,
                    'ict_director_name' => $request->ict_director_name,
                    'ict_director_comments' => $request->ict_director_comments,
                    'ict_director_signature_path' => $request->ict_director_signature_path,
                    'ict_director_approved_at' => $request->ict_director_approved_at,
                    'ict_director_rejection_reasons' => $request->ict_director_rejection_reasons,
                    
                    // Head of IT approval stage
                    'head_it_status' => $request->head_it_status,
                    'head_it_name' => $request->head_it_name,
                    'head_it_comments' => $request->head_it_comments,
                    'head_it_signature_path' => $request->head_it_signature_path,
                    'head_it_approved_at' => $request->head_it_approved_at,
                    
                    // ICT Officer implementation stage
                    'ict_officer_status' => $request->ict_officer_status,
                    'ict_officer_name' => $request->ict_officer_name,
                    'ict_officer_user_id' => $request->ict_officer_user_id,
                    'ict_officer_assigned_at' => $request->ict_officer_assigned_at,
                    'ict_officer_started_at' => $request->ict_officer_started_at,
                    'ict_officer_implemented_at' => $request->ict_officer_implemented_at,
                    'ict_officer_comments' => $request->ict_officer_comments,
                    'ict_officer_signature_path' => $request->ict_officer_signature_path,
                    'implementation_comments' => $request->implementation_comments,
                    
                    // Cancellation/rejection info
                    'cancelled_at' => $request->cancelled_at,
                    'cancelled_by' => $request->cancelled_by,
                    'cancellation_reason' => $request->cancellation_reason,
                    
                    // Timestamps
                    'created_at' => $request->created_at,
                    'updated_at' => $request->updated_at
                ],
                
                // ICT Task Assignment details (for more detailed implementation tracking)
                'ict_assignments' => $request->ictTaskAssignments->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'status' => $assignment->status,
                        'status_label' => $assignment->status_label,
                        'ict_officer' => [
                            'id' => $assignment->ictOfficer->id ?? null,
                            'name' => $assignment->ictOfficer->name ?? 'Unknown Officer',
                            'email' => $assignment->ictOfficer->email ?? null
                        ],
                        'assigned_by' => [
                            'id' => $assignment->assignedBy->id ?? null,
                            'name' => $assignment->assignedBy->name ?? 'System',
                            'email' => $assignment->assignedBy->email ?? null
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
                })->values()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Access request timeline retrieved successfully',
                'data' => $timelineData
            ]);

        } catch (\Exception $e) {
            Log::error('IctOfficerController: Error getting access request timeline', [
                'user_id' => Auth::id(),
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve access request timeline',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get count of pending access requests for notification badge
     */
    public function getPendingRequestsCount()
    {
        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Getting pending requests count', [
                'user_id' => $user->id
            ]);

            // Count unassigned requests (available for assignment)
            // Fixed: Exclude requests that have ANY task assignment (assigned, in_progress, completed, or cancelled)
            $unassignedCount = UserAccess::where('head_it_status', 'approved')
                ->whereNotNull('head_it_approved_at')
                ->whereDoesntHave('ictTaskAssignments')
                ->count();

            // Count requests assigned to this ICT Officer that need attention
            $assignedToMeCount = IctTaskAssignment::where('ict_officer_user_id', $user->id)
                ->whereIn('status', [IctTaskAssignment::STATUS_ASSIGNED, IctTaskAssignment::STATUS_IN_PROGRESS])
                ->count();

            $totalPending = $unassignedCount + $assignedToMeCount;

            return response()->json([
                'success' => true,
                'message' => 'Pending requests count retrieved successfully',
                'data' => [
                    'total_pending' => $totalPending,
                    'unassigned' => $unassignedCount,
                    'assigned_to_me' => $assignedToMeCount,
                    'requires_attention' => $totalPending > 0
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('IctOfficerController: Error getting pending requests count', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pending requests count',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Grant access to a request (final step in the implementation workflow)
     */
    public function grantAccess(Request $request, $requestId)
    {
        try {
            $user = Auth::user();
            
            if (!$user->isIctOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: User is not an ICT Officer'
                ], 403);
            }

            Log::info('IctOfficerController: Granting access for request', [
                'user_id' => $user->id,
                'request_id' => $requestId,
                'comments' => $request->comments
            ]);

            // Validate request data
            $request->validate([
                'comments' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();

            // Find the user access request
            $userAccess = UserAccess::find($requestId);
            if (!$userAccess) {
                Log::error('IctOfficerController: UserAccess not found', [
                    'request_id' => $requestId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Access request not found'
                ], 404);
            }

            // Check if the request is approved by Head of IT
            if ($userAccess->head_it_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access request must be approved by Head of IT before granting access'
                ], 400);
            }

            // Find or create ICT task assignment
            $assignment = IctTaskAssignment::where('user_access_id', $requestId)
                ->where('ict_officer_user_id', $user->id)
                ->first();
                
            if (!$assignment) {
                // Create assignment if it doesn't exist (auto-assign to current officer)
                $assignment = IctTaskAssignment::create([
                    'user_access_id' => $requestId,
                    'ict_officer_user_id' => $user->id,
                    'assigned_by_user_id' => $user->id, // Self-assigned
                    'status' => IctTaskAssignment::STATUS_COMPLETED,
                    'assigned_at' => now(),
                    'started_at' => now(),
                    'completed_at' => now(),
                    'assignment_notes' => 'Auto-assigned during access grant',
                    'completion_notes' => $request->comments ?? 'Access granted by ICT Officer'
                ]);
            } else {
                // Update existing assignment to completed
                $assignment->update([
                    'status' => IctTaskAssignment::STATUS_COMPLETED,
                    'completion_notes' => $request->comments ?? 'Access granted by ICT Officer',
                    'completed_at' => now(),
                    // Set started_at if not already set
                    'started_at' => $assignment->started_at ?: now()
                ]);
            }

            // Update user access record to indicate implementation is complete
            $userAccess->update([
                'ict_officer_status' => 'implemented',
                'ict_officer_user_id' => $user->id,
                'ict_officer_implemented_at' => now(),
                'implementation_comments' => $request->comments,
                // Update overall status to show access is granted
                'status' => 'implemented'
            ]);

            DB::commit();

            Log::info('IctOfficerController: Access granted successfully', [
                'user_id' => $user->id,
                'request_id' => $requestId,
                'assignment_id' => $assignment->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Access granted successfully! The user now has access to the requested modules.',
                'data' => [
                    'request_id' => $requestId,
                    'assignment_id' => $assignment->id,
                    'status' => 'implemented',
                    'granted_at' => $userAccess->ict_officer_implemented_at,
                    'granted_by' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ],
                    'comments' => $request->comments
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('IctOfficerController: Error granting access', [
                'user_id' => Auth::id(),
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to grant access',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
