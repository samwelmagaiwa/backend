<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\UserAccess;
use App\Models\BookingService;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    /**
     * Get user dashboard statistics
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            Log::info('Fetching dashboard stats for user: ' . $user->id);

            // Get User Access Request statistics
            $userAccessStats = $this->getUserAccessStats($user->id);
            
            // Get Booking Service statistics  
            $bookingStats = $this->getBookingStats($user->id);
            
            // Combine statistics
            $combinedStats = [
                'processing' => $userAccessStats['processing'] + $bookingStats['processing'],
                'under_review' => $userAccessStats['under_review'] + $bookingStats['under_review'],
                'completed' => $userAccessStats['completed'] + $bookingStats['completed'],
                'granted_access' => $userAccessStats['granted_access'] + $bookingStats['granted_access'],
                'revision' => $userAccessStats['revision'] + $bookingStats['revision'],
                'needs_revision' => $userAccessStats['needs_revision'] + $bookingStats['needs_revision'],
                'rejected' => $userAccessStats['rejected'] + $bookingStats['rejected'],
                'total' => $userAccessStats['total'] + $bookingStats['total']
            ];

            // Add breakdown by service type
            $breakdown = [
                'user_access' => $userAccessStats,
                'device_booking' => $bookingStats
            ];

            Log::info('Dashboard stats calculated successfully', $combinedStats);

            return response()->json([
                'success' => true,
                'data' => $combinedStats,
                'breakdown' => $breakdown,
                'user_id' => $user->id,
                'generated_at' => Carbon::now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching dashboard stats: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get User Access Request statistics for a user
     */
    private function getUserAccessStats(int $userId): array
    {
        try {
            $baseQuery = UserAccess::where('user_id', $userId);
            
            $stats = [
                'processing' => $baseQuery->clone()->whereIn('status', ['pending'])->count(),
                'under_review' => $baseQuery->clone()->whereIn('status', ['in_review', 'pending'])->count(),
                'completed' => $baseQuery->clone()->where('status', 'approved')->count(),
                'granted_access' => $baseQuery->clone()->where('status', 'approved')->count(),
                'revision' => $baseQuery->clone()->where('status', 'needs_revision')->count(),
                'needs_revision' => $baseQuery->clone()->where('status', 'needs_revision')->count(),
                'rejected' => $baseQuery->clone()->where('status', 'rejected')->count(),
                'total' => $baseQuery->clone()->count()
            ];

            Log::info('User Access stats calculated', [
                'user_id' => $userId,
                'stats' => $stats
            ]);

            return $stats;

        } catch (\Exception $e) {
            Log::error('Error calculating User Access stats: ' . $e->getMessage());
            return [
                'processing' => 0,
                'under_review' => 0,
                'completed' => 0,
                'granted_access' => 0,
                'revision' => 0,
                'needs_revision' => 0,
                'rejected' => 0,
                'total' => 0
            ];
        }
    }

    /**
     * Get Booking Service statistics for a user
     */
    private function getBookingStats(int $userId): array
    {
        try {
            $baseQuery = BookingService::where('user_id', $userId);
            
            $stats = [
                'processing' => $baseQuery->clone()->whereIn('status', ['pending', 'approved'])->count(),
                'under_review' => $baseQuery->clone()->whereIn('status', ['pending'])->count(),
                'completed' => $baseQuery->clone()->whereIn('status', ['returned'])->count(),
                'granted_access' => $baseQuery->clone()->whereIn('status', ['approved', 'in_use', 'returned'])->count(),
                'revision' => 0, // Booking service doesn't have revision status
                'needs_revision' => 0, // Booking service doesn't have revision status
                'rejected' => $baseQuery->clone()->where('status', 'rejected')->count(),
                'total' => $baseQuery->clone()->count()
            ];

            Log::info('Booking Service stats calculated', [
                'user_id' => $userId,
                'stats' => $stats
            ]);

            return $stats;

        } catch (\Exception $e) {
            Log::error('Error calculating Booking Service stats: ' . $e->getMessage());
            return [
                'processing' => 0,
                'under_review' => 0,
                'completed' => 0,
                'granted_access' => 0,
                'revision' => 0,
                'needs_revision' => 0,
                'rejected' => 0,
                'total' => 0
            ];
        }
    }

    /**
     * Get detailed request status breakdown
     */
    public function getRequestStatusBreakdown(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get detailed breakdown by request type
            $userAccessBreakdown = UserAccess::where('user_id', $user->id)
                ->selectRaw('status, COUNT(*) as count, request_type')
                ->groupBy('status', 'request_type')
                ->get()
                ->map(function ($item) {
                    return [
                        'status' => $item->status,
                        'count' => $item->count,
                        'request_type' => $item->request_type,
                        'service_type' => 'user_access'
                    ];
                });

            $bookingBreakdown = BookingService::where('user_id', $user->id)
                ->selectRaw('status, COUNT(*) as count, device_type')
                ->groupBy('status', 'device_type')
                ->get()
                ->map(function ($item) {
                    return [
                        'status' => $item->status,
                        'count' => $item->count,
                        'device_type' => $item->device_type,
                        'service_type' => 'device_booking'
                    ];
                });

            $breakdown = [
                'user_access_requests' => $userAccessBreakdown,
                'device_booking_requests' => $bookingBreakdown
            ];

            return response()->json([
                'success' => true,
                'data' => $breakdown,
                'user_id' => $user->id,
                'generated_at' => Carbon::now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching request status breakdown: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch request status breakdown',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get recent activity for the user
     */
    public function getRecentActivity(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 10);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get recent user access requests
            $userAccessActivity = UserAccess::where('user_id', $user->id)
                ->with('department')
                ->orderBy('updated_at', 'desc')
                ->limit($limit / 2)
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'type' => 'user_access',
                        'title' => 'Access Request',
                        'description' => $request->getRequestTypeNameAttribute(),
                        'status' => $request->status,
                        'date' => $request->updated_at,
                        'department' => $request->department->name ?? 'Unknown'
                    ];
                });

            // Get recent booking requests
            $bookingActivity = BookingService::where('user_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->limit($limit / 2)
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'type' => 'device_booking',
                        'title' => 'Device Booking',
                        'description' => $booking->device_type . ($booking->custom_device ? ' (' . $booking->custom_device . ')' : ''),
                        'status' => $booking->status,
                        'date' => $booking->updated_at,
                        'return_date' => $booking->return_date
                    ];
                });

            // Combine and sort activities
            $allActivity = $userAccessActivity->concat($bookingActivity)
                ->sortByDesc('date')
                ->take($limit)
                ->values();

            return response()->json([
                'success' => true,
                'data' => $allActivity,
                'user_id' => $user->id,
                'generated_at' => Carbon::now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching recent activity: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent activity',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
