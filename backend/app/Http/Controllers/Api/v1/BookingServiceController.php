<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\BookingService;
use App\Models\Department;
use App\Models\DeviceInventory;
use App\Services\SignatureService;
use App\Http\Requests\BookingServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            
            // Check if user already has a pending request
            $existingPendingRequest = BookingService::where('user_id', $request->user()->id)
                ->whereIn('status', ['pending'])
                ->whereIn('ict_approve', ['pending'])
                ->first();
                
            if ($existingPendingRequest) {
                Log::info('User attempted to create booking with existing pending request', [
                    'user_id' => $request->user()->id,
                    'existing_request_id' => $existingPendingRequest->id,
                    'existing_request_status' => $existingPendingRequest->status,
                    'existing_ict_status' => $existingPendingRequest->ict_approve
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot submit a new booking request while you have a pending request. Please wait for your current request to be processed.',
                    'existing_request' => [
                        'id' => $existingPendingRequest->id,
                        'device_type' => $existingPendingRequest->device_type,
                        'custom_device' => $existingPendingRequest->custom_device,
                        'booking_date' => $existingPendingRequest->booking_date,
                        'status' => $existingPendingRequest->status,
                        'ict_approve' => $existingPendingRequest->ict_approve,
                        'created_at' => $existingPendingRequest->created_at,
                        'request_url' => '/request-details?id=' . $existingPendingRequest->id . '&type=booking_service'
                    ]
                ], 422);
            }
            
            try {
                $validatedData = $request->validated();
                Log::info('Validation passed successfully');
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation failed', [
                    'errors' => $e->errors(),
                    'message' => $e->getMessage()
                ]);
                throw $e;
            }
            
            $validatedData['user_id'] = $request->user()->id;
            
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
                
                // If device is available (quantity > 0), reserve it
                if ($availabilityCheck['available']) {
                    if (!$deviceInventory->borrowDevice(1)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unable to reserve the device. It may no longer be available.'
                        ], 400);
                    }
                    
                    // Clear cache when device quantity changes
                    Cache::forget('available_devices');
                    
                    Log::info('Device reserved from inventory', [
                        'device_id' => $deviceInventory->id,
                        'device_name' => $deviceInventory->device_name,
                        'remaining_quantity' => $deviceInventory->available_quantity
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

            // If device was reserved but booking failed, return the device to inventory
            if (isset($deviceInventory) && $deviceInventory) {
                $deviceInventory->returnDevice(1);
                // Clear cache when device quantity changes
                Cache::forget('available_devices');
                Log::info('Device returned to inventory due to booking failure', [
                    'device_id' => $deviceInventory->id
                ]);
            }

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
                'deviceInventory:id,device_name,device_code,description',
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

            $bookingService->update([
                'ict_approve' => 'approved',
                'ict_approved_by' => $request->user()->id,
                'ict_approved_at' => now(),
                'ict_notes' => $request->input('ict_notes', ''),
                // Update main status to approved when ICT approves
                'status' => 'approved',
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
            $pendingRequest = BookingService::where('user_id', $request->user()->id)
                ->whereIn('status', ['pending'])
                ->whereIn('ict_approve', ['pending'])
                ->with(['deviceInventory'])
                ->first();
                
            if ($pendingRequest) {
                return response()->json([
                    'success' => true,
                    'has_pending_request' => true,
                    'message' => 'You have a pending booking request. Please wait for it to be processed before submitting a new request.',
                    'pending_request' => [
                        'id' => $pendingRequest->id,
                        'device_type' => $pendingRequest->device_type,
                        'custom_device' => $pendingRequest->custom_device,
                        'device_name' => $pendingRequest->device_type === 'others' && $pendingRequest->custom_device 
                            ? $pendingRequest->custom_device 
                            : (BookingService::getDeviceTypes()[$pendingRequest->device_type] ?? $pendingRequest->device_type),
                        'booking_date' => $pendingRequest->booking_date,
                        'return_date' => $pendingRequest->return_date,
                        'status' => $pendingRequest->status,
                        'ict_approve' => $pendingRequest->ict_approve,
                        'created_at' => $pendingRequest->created_at,
                        'request_url' => '/request-details?id=' . $pendingRequest->id . '&type=booking_service'
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'has_pending_request' => false,
                'message' => 'No pending requests found. You can submit a new booking request.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking pending requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking pending requests',
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

            $request->validate([
                'device_condition' => 'required|array',
                'device_condition.physical_condition' => 'required|string|in:excellent,good,fair,poor',
                'device_condition.functionality' => 'required|string|in:fully_functional,partially_functional,not_functional',
                'device_condition.accessories_complete' => 'required|boolean',
                'device_condition.visible_damage' => 'required|boolean',
                'device_condition.damage_description' => 'nullable|string|max:500',
                'assessment_notes' => 'nullable|string|max:1000'
            ]);

            $bookingService->update([
                'device_condition_issuing' => $request->device_condition,
                'device_issued_at' => now(),
                'assessed_by' => $request->user()->id,
                'assessment_notes' => $request->assessment_notes,
                'status' => 'in_use' // Update status to in_use when device is issued
            ]);

            $bookingService->load([
                'user:id,name,email,phone,pf_number,department_id',
                'user.department:id,name,code',
                'departmentInfo:id,name,code',
                'deviceInventory:id,device_name,device_code,description',
                'assessedBy:id,name'
            ]);

            Log::info('Device issuing assessment saved', [
                'booking_id' => $bookingService->id,
                'assessed_by' => $request->user()->id,
                'device_condition' => $request->device_condition
            ]);

            return response()->json([
                'success' => true,
                'data' => $bookingService,
                'message' => 'Device condition assessment saved and device marked as issued successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving issuing assessment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving device condition assessment',
                'error' => $e->getMessage()
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
