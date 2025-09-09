<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\BookingService;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * ICT Approval Controller
 * 
 * Handles ICT officer approval/rejection of device booking requests
 * with auto-capture of user details from staff table
 */
class ICTApprovalController extends Controller
{
    /**
     * Get all device borrowing requests with auto-captured user details
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getDeviceBorrowingRequests(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Debug user information
            Log::info('ICT Approval Request - User Info', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_roles' => $user->roles->pluck('name')->toArray(),
                'user_permissions' => $user->getAllPermissions()
            ]);

            // Verify ICT officer permissions with detailed logging
            if (!$this->hasICTPermissions($user)) {
                Log::warning('ICT Approval Access Denied', [
                    'user_id' => $user->id,
                    'user_roles' => $user->roles->pluck('name')->toArray(),
                    'required_permissions' => ['view_device_bookings'],
                    'required_roles' => ['ict_officer', 'admin', 'ict_director']
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ICT officer access required.',
                    'debug' => [
                        'user_roles' => $user->roles->pluck('name')->toArray(),
                        'user_permissions' => $user->getAllPermissions(),
                        'required_roles' => ['ict_officer', 'admin', 'ict_director']
                    ]
                ], 403);
            }

            // Check if booking_service table has any records
            $totalBookings = BookingService::count();
            Log::info('Total booking records in database', ['count' => $totalBookings]);

            $query = BookingService::with([
                'user:id,name,email,phone,pf_number,department_id',
                'user.department:id,name,code',
                'departmentInfo:id,name,code',
                'deviceInventory:id,device_name,device_code,description',
                'approvedBy:id,name'
            ]);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('device_type')) {
                $query->where('device_type', $request->device_type);
            }

            if ($request->filled('department')) {
                $query->where('department', $request->department);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
            }

            // Get paginated results
            $perPage = $request->get('per_page', 50);
            $bookings = $query->orderBy('created_at', 'desc')->paginate($perPage);

            Log::info('Booking query results', [
                'total_found' => $bookings->total(),
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
                'raw_count' => $bookings->count()
            ]);

            // Transform data with auto-captured user details
            $transformedData = $bookings->getCollection()->map(function ($booking) {
                try {
                    return $this->transformBookingForICTApproval($booking);
                } catch (\Exception $e) {
                    Log::error('Error transforming booking data', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                    // Return basic data if transformation fails
                    return [
                        'id' => $booking->id,
                        'borrower_name' => $booking->borrower_name ?? 'Unknown',
                        'device_type' => $booking->device_type,
                        'status' => $booking->status,
                        'booking_date' => $booking->booking_date,
                        'error' => 'Data transformation failed'
                    ];
                }
            });

            // Replace collection with transformed data
            $bookings->setCollection($transformedData);

            Log::info('ICT device borrowing requests retrieved successfully', [
                'total' => $bookings->total(),
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
                'transformed_count' => $transformedData->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => $bookings,
                'message' => 'Device borrowing requests retrieved successfully',
                'debug' => [
                    'total_in_db' => $totalBookings,
                    'filtered_results' => $bookings->total(),
                    'user_has_permission' => true
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving device borrowing requests: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving device borrowing requests',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get detailed information for a specific device borrowing request
     * 
     * @param Request $request
     * @param int $requestId
     * @return JsonResponse
     */
    public function getDeviceBorrowingRequestDetails(Request $request, string $requestId): JsonResponse
    {
        try {
            // Convert string to integer
            $requestIdInt = (int) $requestId;
            
            // Verify ICT officer permissions
            if (!$this->hasICTPermissions($request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ICT officer access required.'
                ], 403);
            }

            $booking = BookingService::with([
                'user:id,name,email,phone,pf_number,department_id',
                'user.department:id,name,code',
                'departmentInfo:id,name,code',
                'deviceInventory:id,device_name,device_code,description',
                'approvedBy:id,name'
            ])->find($requestIdInt);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device borrowing request not found'
                ], 404);
            }

            // Transform with complete user details
            $transformedBooking = $this->transformBookingForICTApproval($booking, true);

            return response()->json([
                'success' => true,
                'data' => $transformedBooking,
                'message' => 'Request details retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving request details: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving request details',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Approve a device borrowing request
     * 
     * @param Request $request
     * @param int $requestId
     * @return JsonResponse
     */
    public function approveDeviceBorrowingRequest(Request $request, int $requestId): JsonResponse
    {
        try {
            // Verify specific approve device bookings permission
            if (!$this->canApproveBookings($request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Device booking approval permission required.'
                ], 403);
            }

            $booking = BookingService::find($requestId);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device borrowing request not found'
                ], 404);
            }

            if (($booking->ict_approve ?? 'pending') !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only approve requests with pending ICT approval status'
                ], 400);
            }

            // Validate input
            $validatedData = $request->validate([
                'ict_notes' => 'nullable|string|max:1000'
            ]);

            // Update ICT approval status (separate from general booking status)
            $booking->update([
                'ict_approve' => 'approved',
                'ict_approved_by' => $request->user()->id,
                'ict_approved_at' => now(),
                'ict_notes' => $validatedData['ict_notes'] ?? null
            ]);

            // Load relationships
            $booking->load(['user', 'approvedBy', 'departmentInfo']);

            Log::info('Device borrowing request approved', [
                'booking_id' => $booking->id,
                'approved_by' => $request->user()->id,
                'borrower_name' => $booking->borrower_name
            ]);

            return response()->json([
                'success' => true,
                'data' => $this->transformBookingForICTApproval($booking),
                'message' => 'Device borrowing request approved successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error approving request: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error approving request',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Reject a device borrowing request
     * 
     * @param Request $request
     * @param int $requestId
     * @return JsonResponse
     */
    public function rejectDeviceBorrowingRequest(Request $request, int $requestId): JsonResponse
    {
        try {
            // Verify specific reject device bookings permission
            if (!$this->canRejectBookings($request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Device booking rejection permission required.'
                ], 403);
            }

            $booking = BookingService::find($requestId);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device borrowing request not found'
                ], 404);
            }

            if (($booking->ict_approve ?? 'pending') !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only reject requests with pending ICT approval status'
                ], 400);
            }

            // Validate input - rejection reason is required
            $validatedData = $request->validate([
                'ict_notes' => 'required|string|max:1000'
            ], [
                'ict_notes.required' => 'Rejection reason is required'
            ]);

            // Update ICT approval status (separate from general booking status)
            $booking->update([
                'ict_approve' => 'rejected',
                'ict_approved_by' => $request->user()->id,
                'ict_approved_at' => now(),
                'ict_notes' => $validatedData['ict_notes']
            ]);

            // If device was reserved, return it to inventory
            if ($booking->device_inventory_id) {
                $deviceInventory = $booking->deviceInventory;
                if ($deviceInventory) {
                    $deviceInventory->returnDevice(1);
                    Log::info('Device returned to inventory due to rejection', [
                        'device_id' => $deviceInventory->id,
                        'device_name' => $deviceInventory->device_name
                    ]);
                }
            }

            // Load relationships
            $booking->load(['user', 'approvedBy', 'departmentInfo']);

            Log::info('Device borrowing request rejected', [
                'booking_id' => $booking->id,
                'rejected_by' => $request->user()->id,
                'borrower_name' => $booking->borrower_name,
                'rejection_reason' => $validatedData['ict_notes']
            ]);

            return response()->json([
                'success' => true,
                'data' => $this->transformBookingForICTApproval($booking),
                'message' => 'Device borrowing request rejected successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error rejecting request: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error rejecting request',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get statistics for device borrowing requests
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getDeviceBorrowingStatistics(Request $request): JsonResponse
    {
        try {
            // Verify specific view booking statistics permission
            if (!$this->canViewStatistics($request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. View booking statistics permission required.'
                ], 403);
            }

            $stats = [
                'pending' => BookingService::where('status', 'pending')->count(),
                'approved' => BookingService::where('status', 'approved')->count(),
                'rejected' => BookingService::where('status', 'rejected')->count(),
                'returned' => BookingService::where('status', 'returned')->count(),
                'compromised' => BookingService::where('status', 'compromised')->count(),
                'total' => BookingService::count()
            ];

            // Monthly statistics for the current year
            $monthlyStats = BookingService::selectRaw('
                MONTH(created_at) as month,
                COUNT(*) as total,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected
            ')
            ->whereYear('created_at', date('Y'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

            // Device type breakdown
            $deviceStats = BookingService::selectRaw('device_type, COUNT(*) as count')
                ->groupBy('device_type')
                ->orderByDesc('count')
                ->get()
                ->pluck('count', 'device_type');

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => $stats,
                    'monthly' => $monthlyStats,
                    'device_breakdown' => $deviceStats
                ],
                'message' => 'Statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving statistics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Auto-capture and link user details to a booking request
     * 
     * @param Request $request
     * @param int $bookingId
     * @return JsonResponse
     */
    public function linkUserDetailsToBooking(Request $request, int $bookingId): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'auto_capture' => 'boolean'
            ]);

            $booking = BookingService::find($bookingId);
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            // Get user details from staff table
            $user = User::with('department')->find($validatedData['user_id']);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Update booking with auto-captured user details
            $booking->update([
                'user_id' => $user->id,
                // User details are already linked via user_id, but we can update other fields if needed
            ]);

            Log::info('User details linked to booking', [
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'department' => $user->department->name ?? 'Unknown'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User details linked to booking successfully',
                'data' => [
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'department' => $user->department->name ?? 'Unknown',
                    'pf_number' => $user->pf_number
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error linking user details: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error linking user details',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Transform booking data for ICT approval system
     * 
     * @param BookingService $booking
     * @param bool $includeDetails
     * @return array
     */
    private function transformBookingForICTApproval(BookingService $booking, bool $includeDetails = false): array
    {
        $user = $booking->user;
        $department = $user->department ?? $booking->departmentInfo;

        $data = [
            // Core request data
            'id' => $booking->id,
            'request_id' => 'REQ-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
            
            // Auto-captured user details from users/staff table
            'borrower_id' => $booking->user_id,
            'borrower_name' => $user->name ?? $booking->borrower_name ?? 'Unknown User',
            'borrower_email' => $user->email ?? null,
            'borrower_phone' => $user->phone ?? $booking->phone_number ?? null,
            'pf_number' => $user->pf_number ?? null,
            
            // Department details
            'department_id' => $department->id ?? null,
            'department' => $department->name ?? 'Unknown Department',
            
            // Device details
            'device_type' => $booking->device_type,
            'custom_device' => $booking->custom_device,
            'device_name' => $this->getDeviceDisplayName($booking->device_type, $booking->custom_device),
            'device_inventory_id' => $booking->device_inventory_id,
            
            // Booking details
            'booking_date' => $booking->booking_date?->format('Y-m-d'),
            'collection_date' => $booking->return_date?->format('Y-m-d'), // Note: return_date is actually collection date
            'return_date' => $booking->return_date?->format('Y-m-d'),
            'return_time' => $booking->return_time,
            'purpose' => $booking->reason,
            
            // Signature details
            'signature' => $booking->signature_path ? true : false,
            'signature_path' => $booking->signature_path,
            'signature_url' => $booking->signature_url,
            'has_signature' => !empty($booking->signature_path),
            
            // Status and approval
            'status' => $booking->status,
            'ict_status' => $booking->ict_approve ?? 'pending', // ICT-specific approval status
            'ict_approve' => $booking->ict_approve ?? 'pending', // For compatibility
            'ict_approval_date' => $booking->ict_approved_at?->format('Y-m-d H:i:s'),
            'ict_notes' => $booking->ict_notes,
            'ict_approved_by' => $booking->ict_approved_by,
            'ict_approved_by_name' => $booking->ictApprovedBy?->name,
            
            // Timestamps
            'created_at' => $booking->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $booking->updated_at?->format('Y-m-d H:i:s')
        ];

        // Include additional details if requested
        if ($includeDetails) {
            $data['device_inventory'] = $booking->deviceInventory ? [
                'id' => $booking->deviceInventory->id,
                'device_name' => $booking->deviceInventory->device_name,
                'device_code' => $booking->deviceInventory->device_code,
                'description' => $booking->deviceInventory->description
            ] : null;

            $data['approved_by'] = $booking->approvedBy ? [
                'id' => $booking->approvedBy->id,
                'name' => $booking->approvedBy->name
            ] : null;
        }

        return $data;
    }

    /**
     * Check if user has ICT permissions for general access
     * 
     * @param User $user
     * @return bool
     */
    private function hasICTPermissions(User $user): bool
    {
        // Load roles if not already loaded
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }
        
        Log::info('Checking ICT permissions', [
            'user_id' => $user->id,
            'user_roles' => $user->roles->pluck('name')->toArray(),
            'user_permissions' => $user->getAllPermissions()
        ]);
        
        // Check for specific view_device_bookings permission first
        if ($user->hasPermission('view_device_bookings')) {
            Log::info('User has view_device_bookings permission');
            return true;
        }
        
        // Fallback to role-based checking for backward compatibility
        $allowedRoles = ['ict_officer', 'admin', 'ict_director'];
        
        // Support both old single role system and new multi-role system
        if ($user->role && $user->role->name) {
            // Old single role system
            $hasRole = in_array($user->role->name, $allowedRoles);
            Log::info('Old role system check', [
                'user_role' => $user->role->name,
                'has_permission' => $hasRole
            ]);
            if ($hasRole) return true;
        }
        
        // New multi-role system
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasAnyRole = !empty(array_intersect($allowedRoles, $userRoles));
        
        Log::info('New role system check', [
            'user_roles' => $userRoles,
            'allowed_roles' => $allowedRoles,
            'has_permission' => $hasAnyRole
        ]);
        
        return $hasAnyRole;
    }
    
    /**
     * Check if user has permission to approve device bookings
     * 
     * @param User $user
     * @return bool
     */
    private function canApproveBookings(User $user): bool
    {
        return $user->hasPermission('approve_device_bookings');
    }
    
    /**
     * Check if user has permission to reject device bookings
     * 
     * @param User $user
     * @return bool
     */
    private function canRejectBookings(User $user): bool
    {
        return $user->hasPermission('reject_device_bookings');
    }
    
    /**
     * Check if user has permission to view booking statistics
     * 
     * @param User $user
     * @return bool
     */
    private function canViewStatistics(User $user): bool
    {
        return $user->hasPermission('view_booking_statistics');
    }
    
    /**
     * Get device display name based on device type and custom device
     * 
     * @param string $deviceType
     * @param string|null $customDevice
     * @return string
     */
    private function getDeviceDisplayName(?string $deviceType, ?string $customDevice = null): string
    {
        if ($deviceType === 'others' && $customDevice) {
            return $customDevice;
        }
        
        $deviceNames = [
            'projector' => 'Projector',
            'tv_remote' => 'TV Remote',
            'hdmi_cable' => 'HDMI Cable',
            'monitor' => 'Monitor',
            'cpu' => 'CPU',
            'keyboard' => 'Keyboard',
            'mouse' => 'Mouse',
            'pc' => 'PC',
            'laptop' => 'Laptop',
            'tablet' => 'Tablet',
            'headphones' => 'Headphones',
            'speaker' => 'Speaker',
            'webcam' => 'Webcam',
            'printer' => 'Printer',
            'others' => 'Other Device'
        ];
        
        return $deviceNames[$deviceType] ?? ucwords(str_replace('_', ' ', $deviceType ?? 'Unknown Device'));
    }
    
    /**
     * Debug endpoint to check ICT approval system status
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function debugICTApprovalSystem(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Check user information
            $userInfo = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray(),
                'permissions' => $user->getAllPermissions(),
                'has_ict_permissions' => $this->hasICTPermissions($user)
            ];
            
            // Check booking service data
            $bookingStats = [
                'total_bookings' => BookingService::count(),
                'pending_bookings' => BookingService::where('status', 'pending')->count(),
                'approved_bookings' => BookingService::where('status', 'approved')->count(),
                'rejected_bookings' => BookingService::where('status', 'rejected')->count(),
                'sample_bookings' => BookingService::with(['user', 'departmentInfo'])
                    ->limit(5)
                    ->get()
                    ->map(function ($booking) {
                        return [
                            'id' => $booking->id,
                            'borrower_name' => $booking->borrower_name,
                            'device_type' => $booking->device_type,
                            'status' => $booking->status,
                            'user_id' => $booking->user_id,
                            'user_name' => $booking->user->name ?? 'No User',
                            'department' => $booking->department,
                            'created_at' => $booking->created_at
                        ];
                    })
            ];
            
            // Check role and permission system
            $roleSystem = [
                'ict_officer_role_exists' => \App\Models\Role::where('name', 'ict_officer')->exists(),
                'admin_role_exists' => \App\Models\Role::where('name', 'admin')->exists(),
                'ict_director_role_exists' => \App\Models\Role::where('name', 'ict_director')->exists(),
                'view_device_bookings_permission_exists' => \App\Models\Role::whereJsonContains('permissions', 'view_device_bookings')->exists()
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user_info' => $userInfo,
                    'booking_stats' => $bookingStats,
                    'role_system' => $roleSystem,
                    'middleware_check' => 'Passed - you can access this endpoint',
                    'timestamp' => now()->toISOString()
                ],
                'message' => 'ICT Approval System Debug Information'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in ICT approval debug: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving debug information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
