<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\BookingService;
use App\Models\Department;
use App\Http\Requests\BookingServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
            
            Log::info('Validated data:', $validatedData);

            // Handle signature upload
            if ($request->hasFile('signature')) {
                $signaturePath = $this->handleSignatureUpload($request->file('signature'), $validatedData['borrower_name']);
                $validatedData['signature_path'] = $signaturePath;
            }

            // Create the booking
            $booking = BookingService::create($validatedData);

            // Load relationships for response
            $booking->load(['user', 'departmentInfo']);

            Log::info('Booking service created successfully', [
                'booking_id' => $booking->id,
                'user_id' => $request->user()->id,
                'device_type' => $booking->device_type
            ]);

            return response()->json([
                'success' => true,
                'data' => $booking,
                'message' => 'Booking submitted successfully! Your request is now pending approval.'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating booking: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'request_data' => $request->except(['signature'])
            ]);

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
            if ((!$request->user()->role || $request->user()->role->name !== 'admin') && $bookingService->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this booking'
                ], 403);
            }

            $bookingService->load(['user', 'approvedBy', 'departmentInfo']);

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
            $departments = Department::select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(function ($dept) {
                    return [
                        'value' => $dept->id,
                        'label' => $dept->name
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $departments,
                'message' => 'Departments retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving departments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving departments',
                'error' => $e->getMessage()
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
}