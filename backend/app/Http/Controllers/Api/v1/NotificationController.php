<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\UserAccess;
use App\Models\IctTaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    /**
     * Get count of pending requests for notification badge (All Roles)
     */
    public function getPendingRequestsCount()
    {
        try {
            $user = Auth::user();
            $roleName = $user->getPrimaryRoleName();
            
            Log::info('NotificationController: Getting pending requests count', [
                'user_id' => $user->id,
                'role' => $roleName
            ]);

            // Create cache key based on user role and ID (for ICT Officers)
            $cacheKey = "notifications_count_{$roleName}" . ($roleName === 'ict_officer' ? "_{$user->id}" : '');
            
            // Try to get from cache first (15 seconds cache)
            $cachedResult = Cache::remember($cacheKey, 15, function() use ($user, $roleName) {
                $pendingCount = 0;
                $details = [];
                
                // Set a reasonable timeout for the database queries
                \DB::statement('SET SESSION wait_timeout = 30');

            switch ($roleName) {
                case 'head_of_department':
                    // Count requests pending HOD approval (newly submitted) - optimized query
                    $pendingCount = UserAccess::where('hod_status', 'pending')
                        ->whereNull('hod_approved_at')
                        ->selectRaw('COUNT(*) as count')
                        ->value('count') ?? 0;
                    
                    $details = [
                        'role' => 'Head of Department',
                        'pending_stage' => 'HOD Approval',
                        'description' => 'New requests awaiting your approval'
                    ];
                    break;

                case 'divisional_director':
                    // Count requests pending Divisional Director approval (approved by HOD) - optimized
                    $pendingCount = UserAccess::where('hod_status', 'approved')
                        ->where('divisional_status', 'pending')
                        ->whereNotNull('hod_approved_at')
                        ->whereNull('divisional_approved_at')
                        ->selectRaw('COUNT(*) as count')
                        ->value('count') ?? 0;
                    
                    $details = [
                        'role' => 'Divisional Director',
                        'pending_stage' => 'Divisional Approval',
                        'description' => 'HOD-approved requests awaiting your approval'
                    ];
                    break;

                case 'ict_director':
                    // Count requests pending ICT Director approval (approved by Divisional Director) - optimized
                    $pendingCount = UserAccess::where('divisional_status', 'approved')
                        ->where('ict_director_status', 'pending')
                        ->whereNotNull('divisional_approved_at')
                        ->whereNull('ict_director_approved_at')
                        ->selectRaw('COUNT(*) as count')
                        ->value('count') ?? 0;
                    
                    $details = [
                        'role' => 'ICT Director',
                        'pending_stage' => 'ICT Director Approval',
                        'description' => 'Divisional-approved requests awaiting your approval'
                    ];
                    break;

                case 'head_of_it':
                    // Count requests pending Head of IT approval (approved by ICT Director) - optimized
                    $pendingCount = UserAccess::where('ict_director_status', 'approved')
                        ->where('head_it_status', 'pending')
                        ->whereNotNull('ict_director_approved_at')
                        ->whereNull('head_it_approved_at')
                        ->selectRaw('COUNT(*) as count')
                        ->value('count') ?? 0;
                    
                    $details = [
                        'role' => 'Head of IT',
                        'pending_stage' => 'Head of IT Approval',
                        'description' => 'ICT Director-approved requests awaiting your approval'
                    ];
                    break;

                case 'ict_officer':
                    // Count unassigned requests (available for assignment) - optimized with timeout
                    $unassignedCount = UserAccess::where('head_it_status', 'approved')
                        ->whereNotNull('head_it_approved_at')
                        ->whereDoesntHave('ictTaskAssignments', function ($query) {
                            $query->whereIn('status', [IctTaskAssignment::STATUS_ASSIGNED, IctTaskAssignment::STATUS_IN_PROGRESS]);
                        })
                        ->selectRaw('COUNT(*) as count')
                        ->value('count') ?? 0;

                    // Count requests assigned to this ICT Officer that need attention - optimized
                    $assignedToMeCount = IctTaskAssignment::where('ict_officer_user_id', $user->id)
                        ->whereIn('status', [IctTaskAssignment::STATUS_ASSIGNED, IctTaskAssignment::STATUS_IN_PROGRESS])
                        ->selectRaw('COUNT(*) as count')
                        ->value('count') ?? 0;

                    $pendingCount = $unassignedCount + $assignedToMeCount;
                    
                    $details = [
                        'role' => 'ICT Officer',
                        'pending_stage' => 'Implementation',
                        'description' => 'Requests requiring implementation',
                        'unassigned' => $unassignedCount,
                        'assigned_to_me' => $assignedToMeCount
                    ];
                    break;

                case 'admin':
                    // Admins can see all pending requests across all stages
                    $hodPending = UserAccess::where('hod_status', 'pending')->count();
                    $divisionalPending = UserAccess::where('hod_status', 'approved')
                        ->where('divisional_status', 'pending')->count();
                    $ictDirectorPending = UserAccess::where('divisional_status', 'approved')
                        ->where('ict_director_status', 'pending')->count();
                    $headItPending = UserAccess::where('ict_director_status', 'approved')
                        ->where('head_it_status', 'pending')->count();
                    $ictOfficerPending = UserAccess::where('head_it_status', 'approved')
                        ->where('ict_officer_status', 'pending')->count();

                    $pendingCount = $hodPending + $divisionalPending + $ictDirectorPending + $headItPending + $ictOfficerPending;
                    
                    $details = [
                        'role' => 'Administrator',
                        'pending_stage' => 'All Stages',
                        'description' => 'System-wide pending requests',
                        'breakdown' => [
                            'hod_pending' => $hodPending,
                            'divisional_pending' => $divisionalPending,
                            'ict_director_pending' => $ictDirectorPending,
                            'head_it_pending' => $headItPending,
                            'ict_officer_pending' => $ictOfficerPending
                        ]
                    ];
                    break;

                default:
                    // Regular staff users don't have pending approval counts
                    $pendingCount = 0;
                    $details = [
                        'role' => 'Staff',
                        'pending_stage' => 'None',
                        'description' => 'No approval responsibilities'
                    ];
                    break;
            }

                return [
                    'total_pending' => $pendingCount,
                    'requires_attention' => $pendingCount > 0,
                    'details' => $details
                ];
            }); // End of Cache::remember function
            
            return response()->json([
                'success' => true,
                'message' => 'Pending requests count retrieved successfully',
                'data' => $cachedResult
            ]);

        } catch (\Exception $e) {
            Log::error('NotificationController: Error getting pending requests count', [
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
     * Get detailed breakdown of pending requests by stage (Admin only)
     */
    public function getPendingRequestsBreakdown()
    {
        try {
            $user = Auth::user();
            
            // Only admins can access full breakdown
            if (!$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: Admin role required'
                ], 403);
            }

            Log::info('NotificationController: Getting pending requests breakdown', [
                'user_id' => $user->id
            ]);

            $breakdown = [
                'hod' => [
                    'count' => UserAccess::where('hod_status', 'pending')->count(),
                    'stage' => 'HOD Approval',
                    'description' => 'Newly submitted requests'
                ],
                'divisional' => [
                    'count' => UserAccess::where('hod_status', 'approved')
                        ->where('divisional_status', 'pending')->count(),
                    'stage' => 'Divisional Director Approval', 
                    'description' => 'HOD-approved requests'
                ],
                'ict_director' => [
                    'count' => UserAccess::where('divisional_status', 'approved')
                        ->where('ict_director_status', 'pending')->count(),
                    'stage' => 'ICT Director Approval',
                    'description' => 'Divisional-approved requests'
                ],
                'head_it' => [
                    'count' => UserAccess::where('ict_director_status', 'approved')
                        ->where('head_it_status', 'pending')->count(),
                    'stage' => 'Head of IT Approval',
                    'description' => 'ICT Director-approved requests'
                ],
                'ict_officer' => [
                    'count' => UserAccess::where('head_it_status', 'approved')
                        ->where('ict_officer_status', 'pending')->count(),
                    'stage' => 'ICT Officer Implementation',
                    'description' => 'Head of IT-approved requests'
                ]
            ];

            $totalPending = collect($breakdown)->sum('count');

            return response()->json([
                'success' => true,
                'message' => 'Pending requests breakdown retrieved successfully',
                'data' => [
                    'total_pending' => $totalPending,
                    'breakdown' => $breakdown,
                    'generated_at' => now()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('NotificationController: Error getting pending requests breakdown', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pending requests breakdown',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
