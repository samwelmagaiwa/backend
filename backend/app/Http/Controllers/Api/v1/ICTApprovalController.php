<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\DeviceInventory;

use App\Http\Controllers\Controller;
use App\Models\BookingService;
use App\Models\User;
use App\Models\Department;
use App\Models\DeviceAssessment;
use App\Models\Signature;
use App\Events\ApprovalStatusChanged;
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
     * @OA\Get(
     *     path="/api/ict-approval/device-requests",
     *     summary="Get Device Borrowing Requests for ICT Approval",
     *     description="Retrieve all device borrowing requests pending ICT officer approval with auto-captured user details",
     *     operationId="getDeviceBorrowingRequests",
     *     tags={"ICT Approval"},
     *     security={"sanctum": {}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by request status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "ict_approved", "approved", "rejected", "completed"})
     *     ),
     *     @OA\Parameter(
     *         name="device_type",
     *         in="query",
     *         description="Filter by device type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="department",
     *         in="query",
     *         description="Filter by department",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Filter by start date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Filter by end date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device borrowing requests retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Device borrowing requests retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=50),
     *                 @OA\Property(property="total", type="integer", example=25),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="borrower_name", type="string", example="John Doe"),
     *                         @OA\Property(property="device_type", type="string", example="Laptop"),
     *                         @OA\Property(property="status", type="string", example="pending"),
     *                         @OA\Property(property="booking_date", type="string", format="date"),
     *                         @OA\Property(property="return_date", type="string", format="date"),
     *                         @OA\Property(property="purpose", type="string", example="Official work")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - ICT officer access required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized. ICT officer access required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving device borrowing requests")
     *         )
     *     )
     * )
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
                'deviceInventory' => function($query) {
                    $query->select(['id', 'device_name', 'device_code', 'description', 'is_active'])
                          ->where('is_active', true);
                },
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
     * @OA\Get(
     *     path="/api/ict-approval/device-requests/{requestId}",
     *     summary="Get Device Borrowing Request Details",
     *     description="Retrieve detailed information for a specific device borrowing request",
     *     operationId="getDeviceBorrowingRequestDetails",
     *     tags={"ICT Approval"},
     *     security={"sanctum": {}},
     *     @OA\Parameter(
     *         name="requestId",
     *         in="path",
     *         description="Device borrowing request ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device borrowing request details retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Request details retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="borrower_name", type="string", example="John Doe"),
     *                 @OA\Property(property="device_type", type="string", example="Laptop"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="booking_date", type="string", format="date"),
     *                 @OA\Property(property="return_date", type="string", format="date"),
     *                 @OA\Property(property="purpose", type="string", example="Official work"),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="phone", type="string", example="+1234567890"),
     *                     @OA\Property(property="pf_number", type="string", example="PF12345")
     *                 ),
     *                 @OA\Property(
     *                     property="device_inventory",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="device_name", type="string", example="Dell Latitude 5420"),
     *                     @OA\Property(property="device_code", type="string", example="DL-001"),
     *                     @OA\Property(property="description", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Device borrowing request not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Device borrowing request not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - ICT officer access required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized. ICT officer access required.")
     *         )
     *     )
     * )
     * 
     * @param Request $request
     * @param string $requestId
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
                'deviceInventory' => function($query) {
                    $query->select(['id', 'device_name', 'device_code', 'description', 'is_active'])
                          ->where('is_active', true);
                },
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
     * Can be done before or after device issuing for workflow flexibility
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
            
            // Do not borrow device at approval stage.
            // Borrowing is performed during issuing assessment to avoid double-counting
            // when the sequence is: approve -> issue.
            Log::info('Approval completed without reserving inventory; issuing step will handle borrowing', [
                'booking_id' => $booking->id,
                'device_inventory_id' => $booking->device_inventory_id,
                'device_issued_at' => $booking->device_issued_at,
                'has_device_condition_issuing' => !empty($booking->device_condition_issuing)
            ]);

            // Load relationships
            $booking->load(['user', 'approvedBy', 'departmentInfo']);

            // Fire SMS notification event
            try {
                $user = $booking->user;
                $approver = $request->user();
                
                ApprovalStatusChanged::dispatch(
                    $user,
                    $booking,
                    'device_booking',
                    'pending',
                    'ict_approved',
                    $approver,
                    $validatedData['ict_notes'] ?? 'Your device booking request has been approved by ICT.'
                );
                
                Log::info('SMS notification event fired for device booking approval', [
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'approver_id' => $approver->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to fire SMS notification for device booking approval', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }

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

            // No need to return device to inventory since it wasn't borrowed during creation
            // Device is only borrowed when approved, so rejection requires no inventory action

            // Load relationships
            $booking->load(['user', 'approvedBy', 'departmentInfo']);

            // Fire SMS notification event
            try {
                $user = $booking->user;
                $approver = $request->user();
                
                ApprovalStatusChanged::dispatch(
                    $user,
                    $booking,
                    'device_booking',
                    'pending',
                    'ict_rejected',
                    $approver,
                    $validatedData['ict_notes']
                );
                
                Log::info('SMS notification event fired for device booking rejection', [
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'approver_id' => $approver->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to fire SMS notification for device booking rejection', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }

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

        // Normalize return status for legacy records where accessories flags
        // incorrectly downgraded the booking to "returned_but_compromised" even
        // when the physical condition and functionality were good and there was
        // no damage recorded.
        $normalizedReturnStatus = $booking->return_status ?? 'not_yet_returned';
        if ($normalizedReturnStatus === 'returned_but_compromised' && !empty($booking->device_condition_receiving)) {
            $receiving = json_decode($booking->device_condition_receiving, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($receiving)) {
                $visibleDamage = $receiving['visible_damage'] ?? false;
                $functionality = $receiving['functionality'] ?? 'fully_functional';
                $physical = $receiving['physical_condition'] ?? 'excellent';

                if ($visibleDamage === false && $functionality === 'fully_functional' && $physical !== 'poor') {
                    // Treat as a clean return; keep the original value in the
                    // database, but surface it to the UI as "returned".
                    $normalizedReturnStatus = 'returned';
                }
            }
        }

        // Determine digital signature status using the same synthetic document_id
        // convention used during booking submission (see BookingServiceRequest).
        $hasDigitalSignature = false;
        if ($booking->user_id) {
            $syntheticDocId = 910000000 + (int) $booking->user_id;
            $hasDigitalSignature = Signature::where('document_id', $syntheticDocId)
                ->where('user_id', $booking->user_id)
                ->exists();
        }

        $data = [
            // Core request data
            'id' => $booking->id,
            'request_id' => 'REQ-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
            
            // Auto-captured user details from users/staff table
            'borrower_id' => $booking->user_id,
            'borrower_name' => $user->name ?? $booking->borrower_name ?? 'Unknown User',
            'borrower_email' => $user->email ?? null,
            'borrower_phone' => $user->phone ?? $booking->phone_number ?? null, // Always prioritize users.phone
            'pf_number' => $user->pf_number ?? null,
            
            // Department details
            'department_id' => $department->id ?? null,
            'department' => $department->name ?? 'Unknown Department',
            
            // Device details (support multi-device bookings)
            'device_type' => $booking->device_type,
            'custom_device' => $booking->custom_device,
            'device_name' => $this->getDeviceDisplayName($booking->device_type, $booking->custom_device, $booking->deviceInventory),
            'device_inventory_id' => $booking->device_inventory_id,
            // All selected inventory devices and their human-readable names
            'device_inventory_ids' => $booking->device_inventory_ids ?? [],
            'all_device_names' => $booking->all_device_names ?? [],
            'device_available' => $booking->deviceInventory ? $booking->deviceInventory->is_active : false,
            
            // Booking details
            'booking_date' => $booking->booking_date?->format('Y-m-d'),
            'collection_date' => $booking->return_date?->format('Y-m-d'), // Note: return_date is actually collection date
            'return_date' => $booking->return_date?->format('Y-m-d'),
            'return_time' => $booking->return_time,
            'purpose' => $booking->reason,
            
            // Signature details (support both legacy file-based and new digital token signatures)
            'signature' => $booking->signature_path ? true : $hasDigitalSignature,
            'signature_path' => $booking->signature_path,
            'signature_url' => $booking->signature_url,
            'has_signature' => !empty($booking->signature_path) || $hasDigitalSignature,
            'digital_signature' => $hasDigitalSignature,
            
            // Status and approval
            'status' => $booking->status,
            'ict_status' => $booking->ict_approve ?? 'pending', // ICT-specific approval status
            'ict_approve' => $booking->ict_approve ?? 'pending', // For compatibility
            'ict_approval_date' => $booking->ict_approved_at?->format('Y-m-d H:i:s'),
            'ict_notes' => $booking->ict_notes,
            'ict_approved_by' => $booking->ict_approved_by,
            'ict_approved_by_name' => $booking->ictApprovedBy?->name,

            // SMS tracking (normalized for UI) - only requester-level for bookings
            'sms_to_requester_status' => $booking->sms_to_requester_status ?? null,
            'sms_sent_to_requester_at' => $booking->sms_sent_to_requester_at,
            'sms_status' => $booking->sms_to_requester_status ?? 'pending',
            
            // Return status
            'return_status' => $normalizedReturnStatus,
            'device_returned_at' => $booking->device_returned_at?->format('Y-m-d H:i:s'),
            
            // Device condition assessment
            'device_condition_issuing' => $booking->device_condition_issuing,
            'device_condition_receiving' => $booking->device_condition_receiving,
            'device_issued_at' => $booking->device_issued_at?->format('Y-m-d H:i:s'),
            'assessed_by' => $booking->assessed_by,
            'assessment_notes' => $booking->assessment_notes,
            
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
        
        // Check for specific booking-related permissions first
        if (
            $user->hasPermission('view_device_bookings') ||
            $user->hasPermission('approve_device_bookings') ||
            $user->hasPermission('manage_device_inventory')
        ) {
            Log::info('User has device booking permissions for ICT approval');
            return true;
        }
        
        // Fallback to role-based checking for backward compatibility
        $allowedRoles = ['ict_officer', 'secretary_ict', 'admin', 'ict_director'];
        
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
     * Get device display name based on device type, custom device, and inventory device
     * 
     * @param string $deviceType
     * @param string|null $customDevice
     * @param DeviceInventory|null $deviceInventory
     * @return string
     */
    private function getDeviceDisplayName(?string $deviceType, ?string $customDevice = null, $deviceInventory = null): string
    {
        // If device is from inventory and still available, use inventory name
        if ($deviceInventory && $deviceInventory->is_active) {
            return $deviceInventory->device_name;
        }
        
        // If device inventory exists but is inactive, show as unavailable
        if ($deviceInventory && !$deviceInventory->is_active) {
            $inventoryName = $deviceInventory->device_name;
            return $inventoryName . ' (Device No Longer Available)';
        }
        
        // If device was linked to inventory but inventory record is deleted
        if (!$deviceInventory && $deviceType) {
            $fallbackName = $this->getFallbackDeviceName($deviceType, $customDevice);
            return $fallbackName . ' (Device No Longer in Inventory)';
        }
        
        // Handle custom devices or fallback to device type mapping
        return $this->getFallbackDeviceName($deviceType, $customDevice);
    }
    
    /**
     * Get fallback device name based on device type and custom device
     * 
     * @param string $deviceType
     * @param string|null $customDevice
     * @return string
     */
    private function getFallbackDeviceName(?string $deviceType, ?string $customDevice = null): string
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
     * Save issuing assessment (when device is issued to borrower)
     * Can be done before or after ICT approval for flexibility
     * 
     * @param Request $request
     * @param int $requestId
     * @return JsonResponse
     */
    public function saveIssuingAssessment(Request $request, int $requestId): JsonResponse
    {
        try {
            // Verify ICT officer permissions
            if (!$this->hasICTPermissions($request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ICT officer access required.'
                ], 403);
            }

            $booking = BookingService::find($requestId);
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device borrowing request not found'
                ], 404);
            }

            // Check if device has already been issued
            if (!empty($booking->device_condition_issuing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device has already been issued. Cannot issue the same device twice.'
                ], 400);
            }

            // Check if request status is suitable for issuing (not rejected)
            if ($booking->ict_approve === 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot issue device for rejected requests'
                ], 400);
            }

            // Normalize notes key from frontend (accept both notes and assessment_notes)
            if ($request->has('assessment_notes') && !$request->has('notes')) {
                $request->merge(['notes' => $request->input('assessment_notes')]);
            }

            // Validate input data
            $validatedData = $request->validate([
                'physical_condition' => 'required|in:excellent,good,fair,poor',
                'functionality' => 'required|in:fully_functional,partially_functional,not_functional',
                'accessories_complete' => 'boolean',
                'visible_damage' => 'boolean',
                'damage_description' => 'nullable|string|max:1000',
                'notes' => 'nullable|string|max:1000'
            ]);

            // Prepare assessment data
            $assessmentData = [
                'physical_condition' => $validatedData['physical_condition'],
                'functionality' => $validatedData['functionality'],
                'accessories_complete' => $validatedData['accessories_complete'] ?? false,
                'visible_damage' => $validatedData['visible_damage'] ?? false,
                'damage_description' => $validatedData['damage_description'] ?? null,
                'assessed_at' => now()->toISOString(),
                'assessed_by' => $request->user()->id,
                'assessment_type' => 'issuing'
            ];

            // Borrow device from inventory if available and not already borrowed
            if ($booking->device_inventory_id) {
                $deviceInventory = $booking->deviceInventory;
                if ($deviceInventory) {
                    if (!$deviceInventory->borrowDevice(1)) {
                        Log::warning('Primary device unavailable during issuing, attempting fallback', [
                            'device_id' => $deviceInventory->id,
                            'device_name' => $deviceInventory->device_name,
                            'available_quantity' => $deviceInventory->available_quantity,
                            'booking_id' => $booking->id
                        ]);
                        // Try to auto-reassign to an alternative available inventory of the same type/name
                        $fallbackName = $this->getFallbackDeviceName($booking->device_type, $booking->custom_device);
                        $alternative = DeviceInventory::active()
                            ->where('device_name', $fallbackName)
                            ->where('available_quantity', '>', 0)
                            ->first();
                        if ($alternative && $alternative->borrowDevice(1)) {
                            // Reassign booking to this inventory
                            $booking->device_inventory_id = $alternative->id;
                            $booking->save();
                            Log::info('Auto-reassigned booking to alternative inventory during issuing', [
                                'old_device_id' => $deviceInventory->id,
                                'new_device_id' => $alternative->id,
                                'new_device_name' => $alternative->device_name,
                                'booking_id' => $booking->id
                            ]);
                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => 'Device is no longer available. Please check device inventory.'
                            ], 400);
                        }
                    }
                    
                    Log::info('Device borrowed from inventory during issuing', [
                        'device_id' => $deviceInventory->id,
                        'device_name' => $deviceInventory->device_name,
                        'remaining_quantity' => $deviceInventory->available_quantity,
                        'booking_id' => $booking->id
                    ]);
                }
            }

            // Save to device_assessments table
            DeviceAssessment::create([
                'booking_id' => $booking->id,
                'assessment_type' => 'issuing',
                'physical_condition' => $validatedData['physical_condition'],
                'functionality' => $validatedData['functionality'],
                'accessories_complete' => $validatedData['accessories_complete'] ?? false,
                'has_damage' => $validatedData['visible_damage'] ?? false,
                'damage_description' => $validatedData['damage_description'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
                'assessed_by' => $request->user()->id,
                'assessed_at' => now()
            ]);

            // Update booking with issuing assessment and set status to in_use
            $booking->update([
                'device_condition_issuing' => json_encode($assessmentData),
                'device_issued_at' => now(),
                'status' => 'in_use', // Set status to in_use when device is issued
                'assessed_by' => $request->user()->id,
                'assessment_notes' => $validatedData['notes'] ?? null
            ]);

            Log::info('Device issuing assessment saved and device borrowed from inventory', [
                'booking_id' => $booking->id,
                'assessed_by' => $request->user()->id,
                'assessment_data' => $assessmentData
            ]);

            return response()->json([
                'success' => true,
                'data' => $this->transformBookingForICTApproval($booking, true),
                'message' => 'Device issued successfully with assessment saved. Device is now in use.'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error saving issuing assessment: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error saving issuing assessment',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Save receiving assessment (when device is received back from borrower)
     * 
     * @param Request $request
     * @param int $requestId
     * @return JsonResponse
     */
    public function saveReceivingAssessment(Request $request, int $requestId): JsonResponse
    {
        try {
            // Verify ICT officer permissions
            if (!$this->hasICTPermissions($request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ICT officer access required.'
                ], 403);
            }

            $booking = BookingService::find($requestId);
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device borrowing request not found'
                ], 404);
            }

            // Validate that device was issued (has issuing assessment)
            if (empty($booking->device_condition_issuing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device must be issued first before it can be received back'
                ], 400);
            }

            // Validate input data
            $validatedData = $request->validate([
                'physical_condition' => 'required|in:excellent,good,fair,poor',
                'functionality' => 'required|in:fully_functional,partially_functional,not_functional',
                'accessories_complete' => 'boolean',
                'visible_damage' => 'boolean',
                'damage_description' => 'nullable|string|max:1000',
                'notes' => 'nullable|string|max:1000'
            ]);

            // Prepare assessment data
            $assessmentData = [
                'physical_condition' => $validatedData['physical_condition'],
                'functionality' => $validatedData['functionality'],
                'accessories_complete' => $validatedData['accessories_complete'] ?? false,
                'visible_damage' => $validatedData['visible_damage'] ?? false,
                'damage_description' => $validatedData['damage_description'] ?? null,
                'assessed_at' => now()->toISOString(),
                'assessed_by' => $request->user()->id,
                'assessment_type' => 'receiving'
            ];

            // Save to device_assessments table
            DeviceAssessment::create([
                'booking_id' => $booking->id,
                'assessment_type' => 'receiving',
                'physical_condition' => $validatedData['physical_condition'],
                'functionality' => $validatedData['functionality'],
                'accessories_complete' => $validatedData['accessories_complete'] ?? false,
                'has_damage' => $validatedData['visible_damage'] ?? false,
                'damage_description' => $validatedData['damage_description'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
                'assessed_by' => $request->user()->id,
                'assessed_at' => now()
            ]);

            // Determine return status based on assessment details
            $returnStatus = 'returned';
            if (
                ($validatedData['visible_damage'] ?? false) === true ||
                ($validatedData['functionality'] ?? 'fully_functional') !== 'fully_functional' ||
                ($validatedData['physical_condition'] ?? 'excellent') === 'poor'
            ) {
                // Mark as compromised only when there is actual damage, reduced
                // functionality, or poor physical condition. Accessories being
                // unchecked/omitted should not on its own downgrade the status.
                $returnStatus = 'returned_but_compromised';
            }

            // Update booking with receiving assessment and mark as returned so user can request again
            $booking->update([
                'device_condition_receiving' => json_encode($assessmentData),
                'device_received_at' => now(),
                'device_returned_at' => now(),
                'status' => 'returned',
                'return_status' => $returnStatus,
                'assessed_by' => $request->user()->id,
                'assessment_notes' => $validatedData['notes'] ?? null
            ]);

            // Return device to inventory if it was reserved
            if ($booking->device_inventory_id) {
                $deviceInventory = $booking->deviceInventory;
                if ($deviceInventory) {
                    $deviceInventory->returnDevice(1);
                    Log::info('Device returned to inventory', [
                        'device_id' => $deviceInventory->id,
                        'device_name' => $deviceInventory->device_name,
                        'booking_id' => $booking->id
                    ]);
                }
            }

            Log::info('Device receiving assessment saved and device returned', [
                'booking_id' => $booking->id,
                'assessed_by' => $request->user()->id,
                'assessment_data' => $assessmentData
            ]);

            return response()->json([
                'success' => true,
                'data' => $this->transformBookingForICTApproval($booking, true),
                'message' => 'Device receiving assessment saved and device marked as returned successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error saving receiving assessment: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error saving receiving assessment',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete a device borrowing request (ICT Officer)
     * 
     * @param Request $request
     * @param int $requestId
     * @return JsonResponse
     */
    public function deleteDeviceBorrowingRequest(Request $request, int $requestId): JsonResponse
    {
        try {
            // Verify ICT officer permissions
            if (!$this->hasICTPermissions($request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ICT officer access required.'
                ], 403);
            }

            $booking = BookingService::find($requestId);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device borrowing request not found'
                ], 404);
            }

            // Check if request can be deleted
            // Allow deletion of: pending requests, rejected requests, and approved requests that have been returned
            $canDelete = false;
            $reason = '';
            
            if ($booking->ict_approve === 'pending' || $booking->status === 'pending') {
                $canDelete = true;
                $reason = 'pending request';
            } elseif ($booking->ict_approve === 'rejected') {
                $canDelete = true;
                $reason = 'rejected request';
            } elseif ($booking->ict_approve === 'approved' && $booking->return_status === 'returned') {
                $canDelete = true;
                $reason = 'approved request that has been returned';
            } elseif ($booking->ict_approve === 'approved' && $booking->return_status === 'returned_but_compromised') {
                $canDelete = true;
                $reason = 'approved request that has been returned (compromised)';
            }
            
            if (!$canDelete) {
                $currentStatus = $booking->ict_approve ?? $booking->status;
                $returnStatus = $booking->return_status ?? 'not_yet_returned';
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete this request. Current ICT status: {$currentStatus}, Return status: {$returnStatus}. Only pending, rejected, or returned requests can be deleted."
                ], 400);
            }
            
            Log::info('Request deletion allowed', [
                'booking_id' => $booking->id,
                'reason' => $reason,
                'ict_approve' => $booking->ict_approve,
                'return_status' => $booking->return_status
            ]);

            // Only return device to inventory if request was approved (device was actually borrowed)
            if ($booking->device_inventory_id && $booking->ict_approve === 'approved') {
                $deviceInventory = $booking->deviceInventory;
                if ($deviceInventory) {
                    $deviceInventory->returnDevice(1);
                    Log::info('Device returned to inventory due to approved request deletion', [
                        'device_id' => $deviceInventory->id,
                        'device_name' => $deviceInventory->device_name,
                        'booking_id' => $booking->id
                    ]);
                } else {
                    Log::info('No device to return - request was not approved', [
                        'booking_id' => $booking->id,
                        'ict_approve' => $booking->ict_approve
                    ]);
                }
            }

            // Delete signature file if exists
            if ($booking->signature_path) {
                \Storage::disk('public')->delete($booking->signature_path);
                Log::info('Signature file deleted', [
                    'signature_path' => $booking->signature_path,
                    'booking_id' => $booking->id
                ]);
            }

            // Store booking info for logging before deletion
            $bookingInfo = [
                'id' => $booking->id,
                'borrower_name' => $booking->borrower_name,
                'device_type' => $booking->device_type,
                'booking_date' => $booking->booking_date,
                'department' => $booking->department
            ];

            // Delete the booking
            $booking->delete();

            Log::info('Device borrowing request deleted by ICT officer', [
                'deleted_by' => $request->user()->id,
                'deleted_by_name' => $request->user()->name,
                'booking_info' => $bookingInfo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Device borrowing request deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting device borrowing request: ' . $e->getMessage(), [
                'request_id' => $requestId,
                'user_id' => $request->user()->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting device borrowing request',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Bulk delete device borrowing requests (ICT Officer)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkDeleteDeviceBorrowingRequests(Request $request): JsonResponse
    {
        try {
            // Verify ICT officer permissions
            if (!$this->hasICTPermissions($request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ICT officer access required.'
                ], 403);
            }

            // Validate request data
            $validatedData = $request->validate([
                'request_ids' => 'required|array|min:1',
                'request_ids.*' => 'required|integer|exists:booking_service,id'
            ]);

            $requestIds = $validatedData['request_ids'];
            $deletedRequests = [];
            $failedRequests = [];
            $returnedDevices = [];

            foreach ($requestIds as $requestId) {
                try {
                    $booking = BookingService::find($requestId);
                    
                    if (!$booking) {
                        $failedRequests[] = [
                            'id' => $requestId,
                            'reason' => 'Request not found'
                        ];
                        continue;
                    }

                    // Check if request can be deleted using same logic as single delete
                    $canDelete = false;
                    $reason = '';
                    
                    if ($booking->ict_approve === 'pending' || $booking->status === 'pending') {
                        $canDelete = true;
                        $reason = 'pending request';
                    } elseif ($booking->ict_approve === 'rejected') {
                        $canDelete = true;
                        $reason = 'rejected request';
                    } elseif ($booking->ict_approve === 'approved' && $booking->return_status === 'returned') {
                        $canDelete = true;
                        $reason = 'approved request that has been returned';
                    } elseif ($booking->ict_approve === 'approved' && $booking->return_status === 'returned_but_compromised') {
                        $canDelete = true;
                        $reason = 'approved request that has been returned (compromised)';
                    }
                    
                    if (!$canDelete) {
                        $currentStatus = $booking->ict_approve ?? $booking->status;
                        $returnStatus = $booking->return_status ?? 'not_yet_returned';
                        $failedRequests[] = [
                            'id' => $requestId,
                            'reason' => "Cannot delete: ICT status: {$currentStatus}, Return status: {$returnStatus}"
                        ];
                        continue;
                    }

                    // If device was reserved, return it to inventory
                    if ($booking->device_inventory_id) {
                        $deviceInventory = $booking->deviceInventory;
                        if ($deviceInventory) {
                            $deviceInventory->returnDevice(1);
                            $returnedDevices[] = [
                                'device_id' => $deviceInventory->id,
                                'device_name' => $deviceInventory->device_name,
                                'booking_id' => $booking->id
                            ];
                        }
                    }

                    // Delete signature file if exists
                    if ($booking->signature_path) {
                        \Storage::disk('public')->delete($booking->signature_path);
                    }

                    // Store booking info for logging before deletion
                    $bookingInfo = [
                        'id' => $booking->id,
                        'borrower_name' => $booking->borrower_name,
                        'device_type' => $booking->device_type,
                        'booking_date' => $booking->booking_date,
                        'department' => $booking->department,
                        'reason' => $reason
                    ];

                    // Delete the booking
                    $booking->delete();
                    $deletedRequests[] = $bookingInfo;

                } catch (\Exception $e) {
                    $failedRequests[] = [
                        'id' => $requestId,
                        'reason' => 'Error: ' . $e->getMessage()
                    ];
                }
            }

            Log::info('Bulk device borrowing requests deletion completed', [
                'deleted_by' => $request->user()->id,
                'deleted_by_name' => $request->user()->name,
                'total_requested' => count($requestIds),
                'successfully_deleted' => count($deletedRequests),
                'failed_deletions' => count($failedRequests),
                'returned_devices' => count($returnedDevices),
                'deleted_requests' => $deletedRequests,
                'failed_requests' => $failedRequests
            ]);

            $message = 'Bulk deletion completed. ';
            $message .= count($deletedRequests) . ' requests deleted successfully.';
            if (count($failedRequests) > 0) {
                $message .= ' ' . count($failedRequests) . ' requests could not be deleted.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'deleted_count' => count($deletedRequests),
                    'failed_count' => count($failedRequests),
                    'deleted_requests' => $deletedRequests,
                    'failed_requests' => $failedRequests,
                    'returned_devices' => $returnedDevices
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error in bulk device borrowing requests deletion: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk deletion',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
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
