<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\BookingService;
use App\Models\Department;
use App\Models\DeviceInventory;
use App\Models\DeviceAssessment;
use App\Services\SignatureService;
use App\Http\Requests\BookingServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = BookingService::with(['user', 'approvedBy', 'departmentInfo']);

            // Filter by user if not admin
            if (!$request->user()->role || $request->user()->role->name !== 'admin') {
                $query->forUser($request->user()->id);
            }

            // Apply filters
            if ($request->has('status')) {
                $query->byStatus($request->status);
            }

            if ($request->has('device_type')) {
                $query->byDeviceType($request->device_type);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->byDateRange($request->start_date, $request->end_date);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $bookings = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $bookings,
                'message' => 'Bookings retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving bookings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingServiceRequest $request): JsonResponse
    {
        try {
            Log::info('BookingService store method called', [
                'user_id' => $request->user()->id,
                'request_data' => $request->except(['signature'])
            ]);
            
            // Check if user can submit a new request (prevent duplicates)
            $canSubmitCheck = BookingService::canUserSubmitNewRequest($request->user()->id);
            
            if (!$canSubmitCheck['can_submit']) {
                $activeRequest = $canSubmitCheck['active_request'];
                
                Log::info('User attempted to create booking with existing active request', [
                    'user_id' => $request->user()->id,
                    'existing_request_id' => $activeRequest->id,
                    'existing_request_status' => $activeRequest->status,
                    'existing_ict_status' => $activeRequest->ict_approve
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $canSubmitCheck['message'],
                    'existing_request' => [
                        'id' => $activeRequest->id,
                        'device_type' => $activeRequest->device_type,
                        'custom_device' => $activeRequest->custom_device,
                        'booking_date' => $activeRequest->booking_date,
                        'status' => $activeRequest->status,
                        'ict_approve' => $activeRequest->ict_approve,
                        'created_at' => $activeRequest->created_at,
                        'device_issued_at' => $activeRequest->device_issued_at,
                        'request_url' => '/request-details?id=' . $activeRequest->id . '&type=booking_service'
                    ]
                ], 422);
            }
            
            $validatedData = $request->validated();
            Log::info('Validation passed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
        
        try {
            $validatedData['user_id'] = $request->user()->id;
        
        // Auto-capture phone number from users table (override any submitted phone_number)
        $user = $request->user();
        if ($user->phone) {
            $validatedData['phone_number'] = $user->phone;
            Log::info('Auto-captured phone number from users table', [
                'user_id' => $user->id,
                'phone_number' => $user->phone
            ]);
        } else {
            Log::warning('User does not have phone number in profile', [
                'user_id' => $user->id
            ]);
        }
            
            // Combine return_date and return_time into return_date_time
            if (isset($validatedData['return_date']) && isset($validatedData['return_time'])) {
                $returnDate = $validatedData['return_date'];
                $returnTime = $validatedData['return_time'];
                $validatedData['return_date_time'] = $returnDate . ' ' . $returnTime;
            }
            
            Log::info('Validated data:', $validatedData);

            // Handle device inventory if device_inventory_id is provided
            $deviceAvailabilityInfo = null;
            if (isset($validatedData['device_inventory_id']) && $validatedData['device_inventory_id']) {
                $deviceInventory = DeviceInventory::find($validatedData['device_inventory_id']);
                
                if (!$deviceInventory) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected device not found in inventory'
                    ], 400);
                }
                
                // Auto-determine device_type from inventory device
                $autoDetectedDeviceType = $this->mapInventoryDeviceToType($deviceInventory);
                if ($autoDetectedDeviceType) {
                    $validatedData['device_type'] = $autoDetectedDeviceType;
                    Log::info('Auto-detected device_type from inventory', [
                        'device_inventory_id' => $deviceInventory->id,
                        'device_name' => $deviceInventory->device_name,
                        'device_code' => $deviceInventory->device_code,
                        'detected_type' => $autoDetectedDeviceType,
                        'original_frontend_type' => $request->input('device_type')
                    ]);
                }
                
                // Check device availability using new enhanced logic
                $availabilityCheck = BookingService::checkDeviceAvailability($validatedData['device_inventory_id']);
                
                if (!$availabilityCheck['can_request']) {
                    return response()->json([
                        'success' => false,
                        'message' => $availabilityCheck['message']
                    ], 400);
                }
                
                // Don't reserve device on booking creation - only when ICT approves
                // Just log the availability check result
                if ($availabilityCheck['available']) {
                    Log::info('Device available for booking request', [
                        'device_id' => $deviceInventory->id,
                        'device_name' => $deviceInventory->device_name,
                        'available_quantity' => $deviceInventory->available_quantity
                    ]);
                } else {
                    // Device is out of stock but request is allowed
                    $deviceAvailabilityInfo = $availabilityCheck;
                    Log::info('Booking request submitted for out-of-stock device', [
                        'device_id' => $deviceInventory->id,
                        'device_name' => $deviceInventory->device_name,
                        'availability_message' => $availabilityCheck['message']
                    ]);
                }
            }

            // Handle signature upload
            if ($request->hasFile('signature')) {
                $signaturePath = $this->handleSignatureUpload($request->file('signature'), $validatedData['borrower_name']);
                $validatedData['signature_path'] = $signaturePath;
            }

            // Create the booking
            $booking = BookingService::create($validatedData);

            // Load relationships for response
            $booking->load(['user', 'departmentInfo', 'deviceInventory']);

            Log::info('Booking service created successfully', [
                'booking_id' => $booking->id,
                'user_id' => $request->user()->id,
                'device_type' => $booking->device_type,
                'device_inventory_id' => $booking->device_inventory_id
            ]);

            // Prepare response message and data
            $responseMessage = 'Booking submitted successfully! Your request is now pending approval.';
            $responseData = [
                'booking' => $booking,
                'queued' => false,
            ];
            
            // If device was out of stock, include availability info in response
            if ($deviceAvailabilityInfo && !$deviceAvailabilityInfo['available']) {
                $responseMessage = 'Booking submitted successfully! ' . $deviceAvailabilityInfo['message'];
                $responseData['availability_info'] = $deviceAvailabilityInfo;
                $responseData['queued'] = true;
            }

            return response()->json([
                'success' => true,
                'data' => $responseData,
                'message' => $responseMessage
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating booking: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'request_data' => $request->except(['signature'])
            ]);

            // No need to return device since we don't reserve it during booking creation
            // Device is only borrowed when ICT approves the request

            return response()->json([
                'success' => false,
                'message' => 'Error submitting booking request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, BookingService $bookingService): JsonResponse
    {
        try {
            // Check if user can view this booking
            $user = $request->user();
            $canView = false;
            
            // Admin can view any booking
            if ($user->hasAnyRole(['admin'])) {
                $canView = true;
            }
            // ICT officers can view any booking for approval purposes
            elseif ($user->hasAnyRole(['ict_officer', 'ict_director'])) {
                $canView = true;
            }
            // Users can view their own bookings
            elseif ($bookingService->user_id === $user->id) {
                $canView = true;
            }
            
            if (!$canView) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this booking'
                ], 403);
            }

            $bookingService->load(['user', 'approvedBy', 'departmentInfo', 'ictApprovedBy']);

            return response()->json([
                'success' => true,
                'data' => $bookingService,
                'message' => 'Booking retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookingServiceRequest $request, BookingService $bookingService): JsonResponse
    {
        try {
            // Check if user can update this booking
            if ((!$request->user()->role || $request->user()->role->name !== 'admin') && $bookingService->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this booking'
                ], 403);
            }

            // Only allow updates if booking is still pending
            if ($bookingService->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update booking that is no longer pending'
                ], 400);
            }

            $validatedData = $request->validated();

            // Handle signature upload if provided
            if ($request->hasFile('signature')) {
                // Delete old signature if exists
                if ($bookingService->signature_path) {
                    Storage::disk('public')->delete($bookingService->signature_path);
                }

                $signaturePath = $this->handleSignatureUpload($request->file('signature'), $validatedData['borrower_name']);
                $validatedData['signature_path'] = $signaturePath;
            }

            $bookingService->update($validatedData);
            $bookingService->load(['user', 'departmentInfo']);

            Log::info('Booking service updated successfully', [
                'booking_id' => $bookingService->id,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => true,
                'data' => $bookingService,
                'message' => 'Booking updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a rejected booking request.
     */
    public function updateRejectedRequest(Request $request, int $bookingId): JsonResponse
    {
        try {
            $booking = BookingService::find($bookingId);
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking request not found'
                ], 404);
            }

            // Check if user can edit this booking (only owner)
            if ($booking->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to edit this booking request'
                ], 403);
            }

            // Only allow editing rejected requests
            if ($booking->ict_approve !== 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only rejected booking requests can be edited'
                ], 400);
            }

            // Use the same validation rules as creation
            $validatedData = $request->validate([
                'device_type' => 'required|string|max:100',
                'custom_device' => 'nullable|string|max:255',
                'device_inventory_id' => 'nullable|integer|exists:device_inventory,id',
                'borrower_name' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'booking_date' => 'required|date|after_or_equal:today',
                'return_date' => 'required|date|after:booking_date',
                'return_time' => 'required',
                'reason' => 'required|string|max:1000',
            ]);

            // Handle signature upload if provided
            if ($request->hasFile('signature')) {
                // Delete old signature if exists
                if ($booking->signature_path) {
                    Storage::disk('public')->delete($booking->signature_path);
                }

                $signaturePath = $this->handleSignatureUpload($request->file('signature'), $validatedData['borrower_name']);
                $validatedData['signature_path'] = $signaturePath;
            }

            // Combine return_date and return_time into return_date_time
            if (isset($validatedData['return_date']) && isset($validatedData['return_time'])) {
                $returnDate = $validatedData['return_date'];
                $returnTime = $validatedData['return_time'];
                $validatedData['return_date_time'] = $returnDate . ' ' . $returnTime;
            }

            // Handle device inventory if device_inventory_id is provided
            if (isset($validatedData['device_inventory_id']) && $validatedData['device_inventory_id']) {
                $deviceInventory = DeviceInventory::find($validatedData['device_inventory_id']);
                
                if (!$deviceInventory) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected device not found in inventory'
                    ], 400);
                }
                
                // Auto-determine device_type from inventory device
                $autoDetectedDeviceType = $this->mapInventoryDeviceToType($deviceInventory);
                if ($autoDetectedDeviceType) {
                    $validatedData['device_type'] = $autoDetectedDeviceType;
                }

                // Check device availability
                $availabilityCheck = BookingService::checkDeviceAvailability($validatedData['device_inventory_id']);
                
                if (!$availabilityCheck['can_request']) {
                    return response()->json([
                        'success' => false,
                        'message' => $availabilityCheck['message']
                    ], 400);
                }
            }

            // Reset ICT approval status to pending for re-evaluation
            $validatedData['ict_approve'] = 'pending';
            $validatedData['ict_approved_by'] = null;
            $validatedData['ict_approved_at'] = null;
            $validatedData['ict_notes'] = null;
            $validatedData['status'] = 'pending';

            // Update the booking
            $booking->update($validatedData);
            $booking->load(['user', 'departmentInfo', 'deviceInventory']);

            Log::info('Rejected booking request updated and re-submitted', [
                'booking_id' => $booking->id,
                'user_id' => $request->user()->id,
                'device_type' => $booking->device_type
            ]);

            return response()->json([
                'success' => true,
                'data' => $booking,
                'message' => 'Booking request updated and re-submitted successfully! Your request is now pending approval again.'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating rejected booking request: ' . $e->getMessage(), [
                'booking_id' => $bookingId,
                'user_id' => $request->user()->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating booking request',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, BookingService $bookingService): JsonResponse
    {
        try {
            // Check if user can delete this booking
            if ((!$request->user()->role || $request->user()->role->name !== 'admin') && $bookingService->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this booking'
                ], 403);
            }

            // Only allow deletion if booking is still pending
            if ($bookingService->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete booking that is no longer pending'
                ], 400);
            }

            // Delete signature file if exists
            if ($bookingService->signature_path) {
                Storage::disk('public')->delete($bookingService->signature_path);
            }

            $bookingService->delete();

            Log::info('Booking service deleted successfully', [
                'booking_id' => $bookingService->id,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get device types for dropdown.
     */
    public function getDeviceTypes(): JsonResponse
    {
        try {
            $deviceTypes = BookingService::getDeviceTypes();
            
            // Format for frontend dropdown
            $formattedDeviceTypes = collect($deviceTypes)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $formattedDeviceTypes,
                'message' => 'Device types retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving device types: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving device types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get departments for dropdown.
     */
    public function getDepartments(): JsonResponse
    {
        try {
            // Get all departments first for debugging
            $allDepartments = Department::all();
            Log::info('All departments in database:', [
                'count' => $allDepartments->count(),
                'departments' => $allDepartments->toArray()
            ]);

            // Get only active departments
            $departments = Department::select('id', 'name', 'code', 'is_active')
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(function ($dept) {
                    return [
                        'id' => $dept->id,
                        'value' => $dept->id,
                        'label' => $dept->name,
                        'name' => $dept->name,
                        'code' => $dept->code
                    ];
                });

            Log::info('Active departments for booking service:', [
                'count' => $departments->count(),
                'departments' => $departments->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $departments,
                'message' => 'Departments retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving departments: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving departments',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get booking statistics.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $query = BookingService::query();

            // Filter by user if not admin
            if (!$request->user()->role || $request->user()->role->name !== 'admin') {
                $query->forUser($request->user()->id);
            }

            $stats = [
                'total_bookings' => $query->count(),
                'pending_bookings' => $query->byStatus('pending')->count(),
                'approved_bookings' => $query->byStatus('approved')->count(),
                'in_use_bookings' => $query->byStatus('in_use')->count(),
                'returned_bookings' => $query->byStatus('returned')->count(),
                'overdue_bookings' => $query->overdue()->count(),
                'rejected_bookings' => $query->byStatus('rejected')->count(),
            ];

            // Device type breakdown
            $deviceStats = BookingService::selectRaw('device_type, COUNT(*) as count')
                ->when(!$request->user()->role || $request->user()->role->name !== 'admin', function ($q) use ($request) {
                    return $q->forUser($request->user()->id);
                })
                ->groupBy('device_type')
                ->get()
                ->pluck('count', 'device_type');

            $stats['device_breakdown'] = $deviceStats;

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle signature file upload.
     */
    private function handleSignatureUpload($file, string $borrowerName): string
    {
        // Sanitize borrower name for folder
        $sanitizedName = Str::slug($borrowerName);
        
        // Create unique filename
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Store in signatures folder organized by borrower name
        $path = "signatures/booking_service/{$sanitizedName}/{$filename}";
        
        // Store the file
        $file->storeAs('public/' . dirname($path), basename($path));
        
        return $path;
    }

    /**
     * Admin: Approve booking.
     */
    public function approve(Request $request, BookingService $bookingService): JsonResponse
    {
        try {
            if (!$request->user()->role || $request->user()->role->name !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            if ($bookingService->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only approve pending bookings'
                ], 400);
            }

            $bookingService->update([
                'status' => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
                'admin_notes' => $request->input('admin_notes')
            ]);

            $bookingService->load(['user', 'approvedBy', 'departmentInfo']);

            Log::info('Booking approved', [
                'booking_id' => $bookingService->id,
                'approved_by' => $request->user()->id
            ]);

            return response()->json([
                'success' => true,
                'data' => $bookingService,
                'message' => 'Booking approved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error approving booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Reject booking.
     */
    public function reject(Request $request, BookingService $bookingService): JsonResponse
    {
        try {
            if (!$request->user()->role || $request->user()->role->name !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            if ($bookingService->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only reject pending bookings'
                ], 400);
            }

            $request->validate([
                'admin_notes' => 'required|string|max:1000'
            ]);

            $bookingService->update([
                'status' => 'rejected',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
                'admin_notes' => $request->input('admin_notes')
            ]);

            $bookingService->load(['user', 'approvedBy', 'departmentInfo']);

            Log::info('Booking rejected', [
                'booking_id' => $bookingService->id,
                'rejected_by' => $request->user()->id
            ]);

            return response()->json([
                'success' => true,
                'data' => $bookingService,
                'message' => 'Booking rejected successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all bookings for ICT approval review (pending, approved, rejected).
     */
    public function getIctApprovalRequests(Request $request): JsonResponse
    {
        try {
            // Check if user has ICT officer role
            if (!$request->user()->hasAnyRole(['ict_officer', 'admin', 'ict_director'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ICT officer access required.'
                ], 403);
            }

            $query = BookingService::with([
                'user:id,name,email,phone,pf_number,department_id',
                'user.department:id,name,code',
                'departmentInfo:id,name,code',
                'deviceInventory' => function($query) {
                    $query->select(['id', 'device_name', 'device_code', 'description', 'is_active'])
                          ->where('is_active', true);
                },
                'ictApprovedBy:id,name'
            ]);

            // Apply filters
            if ($request->filled('ict_status')) {
                $query->byIctApprovalStatus($request->ict_status);
            }

            if ($request->filled('device_type')) {
                $query->byDeviceType($request->device_type);
            }

            if ($request->filled('department')) {
                $query->where('department', $request->department);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('borrower_name', 'LIKE', "%{$search}%")
                      ->orWhere('department', 'LIKE', "%{$search}%")
                      ->orWhere('device_type', 'LIKE', "%{$search}%")
                      ->orWhere('custom_device', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->byDateRange($request->start_date, $request->end_date);
            }

            // Pagination with custom ordering (pending first, then by creation date desc)
            $perPage = $request->get('per_page', 50);
            $bookings = $query->orderByRaw(
                "CASE 
                    WHEN ict_approve = 'pending' THEN 1 
                    WHEN ict_approve = 'approved' THEN 2 
                    WHEN ict_approve = 'rejected' THEN 3 
                    ELSE 4 
                END"
            )->orderBy('created_at', 'desc')->paginate($perPage);

            Log::info('ICT approval requests retrieved', [
                'total' => $bookings->total(),
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => true,
                'data' => $bookings,
                'message' => 'ICT approval requests retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving ICT approval requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving ICT approval requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ICT Officer: Approve booking ICT request.
     */
    public function ictApprove(Request $request, BookingService $bookingService): JsonResponse
    {
        try {
            // Check if user has ICT officer role
            if (!$request->user()->hasAnyRole(['ict_officer', 'admin', 'ict_director'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ICT officer access required.'
                ], 403);
            }

            if ($bookingService->ict_approve !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only approve pending ICT requests'
                ], 400);
            }

            // Prepare approval data
            $approvalData = [
                'ict_approve' => 'approved',
                'ict_approved_by' => $request->user()->id,
                'ict_approved_at' => now(),
                'ict_notes' => $request->input('ict_notes', ''),
                'approved_by' => $request->user()->id,
                'approved_at' => now()
            ];
            
            // If device assessment already exists, mark device as issued and in_use
            if ($bookingService->device_condition_issuing) {
                $approvalData['device_issued_at'] = now();
                $approvalData['status'] = 'in_use';
            } else {
                // If no assessment yet, just mark as approved
                $approvalData['status'] = 'approved';
            }
            
            $bookingService->update($approvalData);

            $bookingService->load([
                'user:id,name,email,phone,pf_number,department_id',
                'user.department:id,name,code',
                'departmentInfo:id,name,code',
                'deviceInventory:id,device_name,device_code,description',
                'ictApprovedBy:id,name'
            ]);

            Log::info('Booking ICT approved and main status updated', [
                'booking_id' => $bookingService->id,
                'ict_approved_by' => $request->user()->id,
                'borrower_name' => $bookingService->borrower_name,
                'main_status' => 'approved',
                'ict_status' => 'approved'
            ]);

            return response()->json([
                'success' => true,
                'data' => $bookingService,
                'message' => 'Booking approved successfully by ICT Officer'
            ]);

        } catch (\Exception $e) {
            Log::error('Error ICT approving booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error ICT approving booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ICT Officer: Reject booking ICT request.
     */
    public function ictReject(Request $request, BookingService $bookingService): JsonResponse
    {
        try {
            // Check if user has ICT officer role
            if (!$request->user()->hasAnyRole(['ict_officer', 'admin', 'ict_director'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ICT officer access required.'
                ], 403);
            }

            if ($bookingService->ict_approve !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only reject pending ICT requests'
                ], 400);
            }

            $request->validate([
                'ict_notes' => 'required|string|max:1000'
            ]);

            $bookingService->update([
                'ict_approve' => 'rejected',
                'ict_approved_by' => $request->user()->id,
                'ict_approved_at' => now(),
                'ict_notes' => $request->input('ict_notes'),
                // Update main status to rejected when ICT rejects
                'status' => 'rejected',
                'approved_by' => $request->user()->id,
                'approved_at' => now()
            ]);

            $bookingService->load([
                'user:id,name,email,phone,pf_number,department_id',
                'user.department:id,name,code',
                'departmentInfo:id,name,code',
                'deviceInventory:id,device_name,device_code,description',
                'ictApprovedBy:id,name'
            ]);

            Log::info('Booking ICT rejected and main status updated', [
                'booking_id' => $bookingService->id,
                'ict_rejected_by' => $request->user()->id,
                'borrower_name' => $bookingService->borrower_name,
                'reason' => $request->input('ict_notes'),
                'main_status' => 'rejected',
                'ict_status' => 'rejected'
            ]);

            return response()->json([
                'success' => true,
                'data' => $bookingService,
                'message' => 'Booking rejected successfully by ICT Officer'
            ]);

        } catch (\Exception $e) {
            Log::error('Error ICT rejecting booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error ICT rejecting booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug endpoint to check departments.
     */
    public function debugDepartments(): JsonResponse
    {
        try {
            $allDepartments = Department::all();
            $activeDepartments = Department::where('is_active', true)->get();
            $inactiveDepartments = Department::where('is_active', false)->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_departments' => $allDepartments->count(),
                    'active_departments' => $activeDepartments->count(),
                    'inactive_departments' => $inactiveDepartments->count(),
                    'all_departments' => $allDepartments->map(function ($dept) {
                        return [
                            'id' => $dept->id,
                            'name' => $dept->name,
                            'code' => $dept->code,
                            'is_active' => $dept->is_active,
                            'created_at' => $dept->created_at
                        ];
                    }),
                    'database_connection' => \DB::connection()->getDatabaseName(),
                    'table_exists' => \Schema::hasTable('departments')
                ],
                'message' => 'Debug information retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in debug departments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving debug information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has any pending booking requests.
     */
    public function checkPendingRequests(Request $request): JsonResponse
    {
        try {
            // Check if user can submit a new request
            $canSubmitCheck = BookingService::canUserSubmitNewRequest($request->user()->id);
            
            if (!$canSubmitCheck['can_submit']) {
                $activeRequest = $canSubmitCheck['active_request'];
                
                // Load device inventory relationship
                $activeRequest->load(['deviceInventory']);
                
                // Determine request type for frontend
                $requestType = '';
                if ($activeRequest->status === 'pending' && $activeRequest->ict_approve === 'pending') {
                    $requestType = 'pending';
                } else {
                    $requestType = 'active';
                }
                
                return response()->json([
                    'success' => true,
                    'has_pending_request' => true,
                    'has_active_request' => true,
                    'request_type' => $requestType,
                    'message' => $canSubmitCheck['message'],
                    'active_request' => [
                        'id' => $activeRequest->id,
                        'device_type' => $activeRequest->device_type,
                        'custom_device' => $activeRequest->custom_device,
                        'device_name' => $activeRequest->device_type === 'others' && $activeRequest->custom_device 
                            ? $activeRequest->custom_device 
                            : (BookingService::getDeviceTypes()[$activeRequest->device_type] ?? $activeRequest->device_type),
                        'booking_date' => $activeRequest->booking_date,
                        'return_date' => $activeRequest->return_date,
                        'status' => $activeRequest->status,
                        'ict_approve' => $activeRequest->ict_approve,
                        'created_at' => $activeRequest->created_at,
                        'device_issued_at' => $activeRequest->device_issued_at,
                        'request_url' => '/request-details?id=' . $activeRequest->id . '&type=booking_service'
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'has_pending_request' => false,
                'has_active_request' => false,
                'message' => 'No active requests found. You can submit a new booking request.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking active requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking active requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check device availability for borrowing.
     */
    public function checkDeviceAvailability(Request $request, int $deviceInventoryId): JsonResponse
    {
        try {
            $availabilityInfo = BookingService::checkDeviceAvailability($deviceInventoryId);
            
            return response()->json([
                'success' => true,
                'data' => $availabilityInfo,
                'message' => 'Device availability checked successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking device availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking device availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active bookings for a specific device.
     */
    public function getDeviceBookings(Request $request, int $deviceInventoryId): JsonResponse
    {
        try {
            $bookings = BookingService::getActiveBookingsForDevice($deviceInventoryId);
            
            return response()->json([
                'success' => true,
                'data' => $bookings,
                'message' => 'Device bookings retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving device bookings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving device bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Seed departments if they don't exist.
     */
    public function seedDepartments(): JsonResponse
    {
        try {
            $existingCount = Department::count();
            
            if ($existingCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Departments already exist ({$existingCount} found). No seeding needed.",
                    'data' => [
                        'existing_count' => $existingCount,
                        'action' => 'none'
                    ]
                ]);
            }

            // Run the department seeder
            \Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DepartmentSeeder']);
            
            $newCount = Department::count();
            
            Log::info('Departments seeded successfully', [
                'previous_count' => $existingCount,
                'new_count' => $newCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Departments seeded successfully',
                'data' => [
                    'previous_count' => $existingCount,
                    'new_count' => $newCount,
                    'action' => 'seeded'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error seeding departments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error seeding departments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug endpoint to check database schema for assessment columns.
     */
    public function debugAssessmentSchema(): JsonResponse
    {
        try {
            $tableColumns = \Schema::getColumnListing('booking_service');
            $requiredColumns = [
                'device_condition_issuing', 
                'device_condition_receiving',
                'device_issued_at',
                'device_received_at', 
                'assessed_by', 
                'assessment_notes'
            ];
            
            $existingColumns = array_intersect($requiredColumns, $tableColumns);
            $missingColumns = array_diff($requiredColumns, $tableColumns);
            
            // Check if migrations have been run
            $migrationFiles = [
                '2025_01_27_160000_add_device_condition_assessment_to_booking_service_table.php',
                '2025_09_10_000000_create_device_assessments_table.php'
            ];
            
            $migrationStatus = [];
            foreach ($migrationFiles as $migration) {
                $migrationName = str_replace('.php', '', $migration);
                $exists = \DB::table('migrations')
                    ->where('migration', $migrationName)
                    ->exists();
                $migrationStatus[$migration] = $exists ? 'run' : 'not_run';
            }
            
            // Sample booking for testing
            $sampleBooking = BookingService::with(['user', 'assessedBy'])
                ->where('ict_approve', 'approved')
                ->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'table_exists' => \Schema::hasTable('booking_service'),
                    'total_columns' => count($tableColumns),
                    'all_columns' => $tableColumns,
                    'required_assessment_columns' => $requiredColumns,
                    'existing_assessment_columns' => $existingColumns,
                    'missing_assessment_columns' => $missingColumns,
                    'schema_complete' => empty($missingColumns),
                    'migration_status' => $migrationStatus,
                    'sample_booking_id' => $sampleBooking?->id,
                    'sample_booking_status' => $sampleBooking?->ict_approve,
                    'database_connection' => \DB::connection()->getDatabaseName(),
                    'recommendations' => empty($missingColumns) 
                        ? ['Schema is complete - ready for assessments']
                        : [
                            'Run missing migrations: php artisan migrate',
                            'Missing columns: ' . implode(', ', $missingColumns)
                        ]
                ],
                'message' => 'Database schema analysis completed'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in debug assessment schema: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing database schema',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map device inventory to valid device_type enum value.
     */
    private function mapInventoryDeviceToType(DeviceInventory $device): ?string
    {
        $validDeviceTypes = [
            'projector',
            'tv_remote', 
            'hdmi_cable',
            'monitor',
            'cpu',
            'keyboard',
            'pc',
            'others'
        ];

        // First, try to use device_code if it's a valid ENUM value
        if ($device->device_code && in_array(strtolower($device->device_code), $validDeviceTypes)) {
            return strtolower($device->device_code);
        }
        
        // Special mapping for device_code that doesn't match enum exactly
        if ($device->device_code) {
            $deviceCodeLower = strtolower($device->device_code);
            if ($deviceCodeLower === 'hdmi') {
                return 'hdmi_cable';
            }
        }

        // Fallback to device_name mapping
        $deviceNameLower = strtolower($device->device_name);
        
        // Direct matches
        if (str_contains($deviceNameLower, 'projector')) return 'projector';
        if (str_contains($deviceNameLower, 'monitor')) return 'monitor';
        if (str_contains($deviceNameLower, 'cpu')) return 'cpu';
        if (str_contains($deviceNameLower, 'keyboard')) return 'keyboard';
        if (str_contains($deviceNameLower, 'pc') || str_contains($deviceNameLower, 'computer')) return 'pc';
        if (str_contains($deviceNameLower, 'tv') && str_contains($deviceNameLower, 'remote')) return 'tv_remote';
        if (str_contains($deviceNameLower, 'hdmi')) return 'hdmi_cable';
        
        // Partial matches for common device types
        if (str_contains($deviceNameLower, 'laptop') || str_contains($deviceNameLower, 'notebook')) return 'pc';
        if (str_contains($deviceNameLower, 'screen') || str_contains($deviceNameLower, 'display')) return 'monitor';
        if (str_contains($deviceNameLower, 'remote') && !str_contains($deviceNameLower, 'hdmi')) return 'tv_remote';
        if (str_contains($deviceNameLower, 'cable') && !str_contains($deviceNameLower, 'hdmi')) return 'hdmi_cable';
        
        // Additional matches
        if (str_contains($deviceNameLower, 'tv') || str_contains($deviceNameLower, 'television')) return 'tv_remote';
        if (str_contains($deviceNameLower, 'desktop')) return 'pc';
        if (str_contains($deviceNameLower, 'project')) return 'projector';
        
        // For inventory devices that don't match any pattern, use 'others'
        Log::info('Device name not recognized in backend mapping, using others', [
            'device_id' => $device->id,
            'device_name' => $device->device_name,
            'device_code' => $device->device_code
        ]);
        
        return 'others';
    }

    /**
     * ICT Officer: Save device condition assessment when issuing device.
     */
    public function saveIssuingAssessment(Request $request, BookingService $bookingService): JsonResponse
    {
        try {
            // Check if user has ICT officer role
            if (!$request->user()->hasAnyRole(['ict_officer', 'admin', 'ict_director'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ICT officer access required.'
                ], 403);
            }

            Log::info('Starting issuing assessment save', [
                'booking_id' => $bookingService->id,
                'user_id' => $request->user()->id,
                'request_data' => $request->all()
            ]);

            // Validate request data
            $validatedData = $request->validate([
                'device_condition' => 'required|array',
                'device_condition.physical_condition' => 'required|string|in:excellent,good,fair,poor',
                'device_condition.functionality' => 'required|string|in:fully_functional,partially_functional,not_functional',
                'device_condition.accessories_complete' => 'required|boolean',
                'device_condition.visible_damage' => 'required|boolean',
                'device_condition.damage_description' => 'nullable|string|max:500',
                'assessment_notes' => 'nullable|string|max:1000'
            ]);

            Log::info('Validation passed', ['validated_data' => $validatedData]);

            // Check if the booking exists and can be updated
            if (!$bookingService->exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking service record not found'
                ], 404);
            }

            // Check if the booking is in a valid state for assessment
            // Allow assessment for pending requests (before approval) and approved requests
            if (!in_array($bookingService->ict_approve, ['pending', 'approved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device assessment can only be done for pending or approved requests. Current ICT status: ' . $bookingService->ict_approve
                ], 400);
            }

            // Prepare update data
            $updateData = [
                'device_condition_issuing' => $validatedData['device_condition'],
                'assessed_by' => $request->user()->id,
                'assessment_notes' => $validatedData['assessment_notes'] ?? null
            ];
            
            // Only mark as issued and in_use if the request is already approved
            if ($bookingService->ict_approve === 'approved') {
                $updateData['device_issued_at'] = now();
                $updateData['status'] = 'in_use';
            }

            Log::info('Attempting to update booking service', [
                'booking_id' => $bookingService->id,
                'update_data' => $updateData
            ]);

            // Check if the database columns exist
            $tableColumns = \Schema::getColumnListing('booking_service');
            $requiredColumns = ['device_condition_issuing', 'device_issued_at', 'assessed_by', 'assessment_notes'];
            $missingColumns = array_diff($requiredColumns, $tableColumns);
            
            if (!empty($missingColumns)) {
                Log::error('Missing database columns', [
                    'missing_columns' => $missingColumns,
                    'existing_columns' => $tableColumns
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Database schema error: Missing columns: ' . implode(', ', $missingColumns),
                    'error' => 'Please run database migrations'
                ], 500);
            }

            // Check if assessment already exists to prevent duplicates
            $existingAssessment = DeviceAssessment::where('booking_id', $bookingService->id)
                ->where('assessment_type', 'issuing')
                ->first();
            
            if ($existingAssessment) {
                // Update existing assessment instead of creating a new one
                $existingAssessment->update([
                    'physical_condition' => $validatedData['device_condition']['physical_condition'],
                    'functionality' => $validatedData['device_condition']['functionality'],
                    'accessories_complete' => $validatedData['device_condition']['accessories_complete'],
                    'has_damage' => $validatedData['device_condition']['visible_damage'],
                    'damage_description' => $validatedData['device_condition']['damage_description'] ?? null,
                    'notes' => $validatedData['assessment_notes'] ?? null,
                    'assessed_by' => $request->user()->id,
                    'assessed_at' => now()
                ]);
                
                Log::info('Updated existing issuing assessment', [
                    'assessment_id' => $existingAssessment->id,
                    'booking_id' => $bookingService->id
                ]);
            } else {
                // Create new assessment
                DeviceAssessment::create([
                    'booking_id' => $bookingService->id,
                    'assessment_type' => 'issuing',
                    'physical_condition' => $validatedData['device_condition']['physical_condition'],
                    'functionality' => $validatedData['device_condition']['functionality'],
                    'accessories_complete' => $validatedData['device_condition']['accessories_complete'],
                    'has_damage' => $validatedData['device_condition']['visible_damage'],
                    'damage_description' => $validatedData['device_condition']['damage_description'] ?? null,
                    'notes' => $validatedData['assessment_notes'] ?? null,
                    'assessed_by' => $request->user()->id,
                    'assessed_at' => now()
                ]);
                
                Log::info('Created new issuing assessment', [
                    'booking_id' => $bookingService->id
                ]);
            }

            // Perform the update (for backward compatibility)
            $updated = $bookingService->update($updateData);
            
            if (!$updated) {
                Log::error('Failed to update booking service record', [
                    'booking_id' => $bookingService->id,
                    'update_data' => $updateData
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update booking service record'
                ], 500);
            }

            Log::info('Successfully updated booking service', [
                'booking_id' => $bookingService->id
            ]);

            // Reload the model with relationships
            $bookingService->load([
                'user:id,name,email,phone,pf_number,department_id',
                'user.department:id,name,code',
                'departmentInfo:id,name,code',
                'deviceInventory:id,device_name,device_code,description',
                'assessedBy:id,name'
            ]);

            Log::info('Device issuing assessment saved successfully', [
                'booking_id' => $bookingService->id,
                'assessed_by' => $request->user()->id,
                'device_condition' => $validatedData['device_condition']
            ]);

            return response()->json([
                'success' => true,
                'data' => $bookingService,
                'message' => 'Device condition assessment saved and device marked as issued successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in issuing assessment', [
                'booking_id' => $bookingService->id ?? 'unknown',
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in issuing assessment', [
                'booking_id' => $bookingService->id ?? 'unknown',
                'sql_error' => $e->getMessage(),
                'sql_code' => $e->getCode()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => config('app.debug') ? $e->getMessage() : 'Database operation failed'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Unexpected error in issuing assessment', [
                'booking_id' => $bookingService->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving device condition assessment',
                'error' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * ICT Officer: Save device condition assessment when receiving device back.
     */
    public function saveReceivingAssessment(Request $request, BookingService $bookingService): JsonResponse
    {
        try {
            // Check if user has ICT officer role
            if (!$request->user()->hasAnyRole(['ict_officer', 'admin', 'ict_director'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. ICT officer access required.'
                ], 403);
            }

            $request->validate([
                'device_condition' => 'required|array',
                'device_condition.physical_condition' => 'required|string|in:excellent,good,fair,poor',
                'device_condition.functionality' => 'required|string|in:fully_functional,partially_functional,not_functional',
                'device_condition.accessories_complete' => 'required|boolean',
                'device_condition.visible_damage' => 'required|boolean',
                'device_condition.damage_description' => 'nullable|string|max:500',
                'assessment_notes' => 'nullable|string|max:1000'
            ]);

            // Determine return status based on condition assessment
            $returnStatus = 'returned';
            $deviceCondition = $request->device_condition;
            
            // Mark as compromised if device has issues
            if ($deviceCondition['physical_condition'] === 'poor' || 
                $deviceCondition['functionality'] !== 'fully_functional' ||
                $deviceCondition['visible_damage'] === true ||
                !$deviceCondition['accessories_complete']) {
                $returnStatus = 'returned_but_compromised';
            }

            // Check if receiving assessment already exists to prevent duplicates
            $existingReceivingAssessment = DeviceAssessment::where('booking_id', $bookingService->id)
                ->where('assessment_type', 'receiving')
                ->first();
            
            if ($existingReceivingAssessment) {
                // Update existing assessment instead of creating a new one
                $existingReceivingAssessment->update([
                    'physical_condition' => $deviceCondition['physical_condition'],
                    'functionality' => $deviceCondition['functionality'],
                    'accessories_complete' => $deviceCondition['accessories_complete'],
                    'has_damage' => $deviceCondition['visible_damage'],
                    'damage_description' => $deviceCondition['damage_description'] ?? null,
                    'notes' => $request->assessment_notes ?? null,
                    'assessed_by' => $request->user()->id,
                    'assessed_at' => now()
                ]);
                
                Log::info('Updated existing receiving assessment', [
                    'assessment_id' => $existingReceivingAssessment->id,
                    'booking_id' => $bookingService->id
                ]);
            } else {
                // Create new assessment
                DeviceAssessment::create([
                    'booking_id' => $bookingService->id,
                    'assessment_type' => 'receiving',
                    'physical_condition' => $deviceCondition['physical_condition'],
                    'functionality' => $deviceCondition['functionality'],
                    'accessories_complete' => $deviceCondition['accessories_complete'],
                    'has_damage' => $deviceCondition['visible_damage'],
                    'damage_description' => $deviceCondition['damage_description'] ?? null,
                    'notes' => $request->assessment_notes ?? null,
                    'assessed_by' => $request->user()->id,
                    'assessed_at' => now()
                ]);
                
                Log::info('Created new receiving assessment', [
                    'booking_id' => $bookingService->id
                ]);
            }

            // Update booking (for backward compatibility)
            $bookingService->update([
                'device_condition_receiving' => $request->device_condition,
                'device_received_at' => now(),
                'assessed_by' => $request->user()->id,
                'assessment_notes' => $request->assessment_notes,
                'return_status' => $returnStatus,
                'status' => 'returned', // Update main status to returned
                'device_returned_at' => now()
            ]);

            // Return device to inventory
            if ($bookingService->device_inventory_id) {
                $deviceInventory = $bookingService->deviceInventory;
                if ($deviceInventory) {
                    $deviceInventory->returnDevice(1);
                    Log::info('Device returned to inventory', [
                        'device_id' => $deviceInventory->id,
                        'device_name' => $deviceInventory->device_name,
                        'booking_id' => $bookingService->id
                    ]);
                }
            }

            $bookingService->load([
                'user:id,name,email,phone,pf_number,department_id',
                'user.department:id,name,code',
                'departmentInfo:id,name,code',
                'deviceInventory:id,device_name,device_code,description',
                'assessedBy:id,name'
            ]);

            Log::info('Device receiving assessment saved', [
                'booking_id' => $bookingService->id,
                'assessed_by' => $request->user()->id,
                'return_status' => $returnStatus,
                'device_condition' => $request->device_condition
            ]);

            return response()->json([
                'success' => true,
                'data' => $bookingService,
                'message' => 'Device received and condition assessment completed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving receiving assessment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving device condition assessment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
