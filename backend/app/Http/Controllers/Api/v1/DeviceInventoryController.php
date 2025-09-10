<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeviceInventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeviceInventoryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the devices.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DeviceInventory::with(['createdBy', 'updatedBy']);

            // Apply filters
            if ($request->has('active_only') && $request->active_only) {
                $query->active();
            }

            if ($request->has('available_only') && $request->available_only) {
                $query->available();
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('device_name', 'like', "%{$search}%")
                      ->orWhere('device_code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Sort by name by default
            $sortBy = $request->get('sort_by', 'device_name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $devices = $query->paginate($perPage);

            // Transform data for response
            $transformedDevices = $devices->getCollection()->map(function ($device) {
                // Get return status information
                $returnStatusInfo = $this->getDeviceReturnStatusInfo($device->id);
                
                return [
                    'id' => $device->id,
                    'device_name' => $device->device_name,
                    'device_code' => $device->device_code,
                    'description' => $device->description,
                    'total_quantity' => $device->total_quantity,
                    'available_quantity' => $device->available_quantity,
                    'borrowed_quantity' => $device->borrowed_quantity,
                    'utilization_percentage' => $device->utilization_percentage,
                    'availability_status' => $device->availability_status,
                    'is_active' => $device->is_active,
                    'can_borrow' => $device->isAvailable(),
                    'created_by' => $device->createdBy ? $device->createdBy->name : null,
                    'updated_by' => $device->updatedBy ? $device->updatedBy->name : null,
                    'created_at' => $device->created_at->toISOString(),
                    'updated_at' => $device->updated_at->toISOString(),
                    // Return status aggregated information
                    'return_status_summary' => $returnStatusInfo['summary'],
                    'unreturned_count' => $returnStatusInfo['unreturned_count'],
                    'compromised_count' => $returnStatusInfo['compromised_count'],
                    'returned_count' => $returnStatusInfo['returned_count'],
                    'total_bookings' => $returnStatusInfo['total_bookings'],
                ];
            });

            $devices->setCollection($transformedDevices);

            return response()->json([
                'success' => true,
                'data' => $devices,
                'message' => 'Devices retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving devices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving devices',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created device.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'total_quantity' => 'required|integer|min:0|max:10000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate unique device code
            $deviceCode = $this->generateDeviceCode($request->device_name);

            $device = DeviceInventory::create([
                'device_name' => $request->device_name,
                'device_code' => $deviceCode,
                'description' => $request->description,
                'total_quantity' => $request->total_quantity,
                'available_quantity' => $request->total_quantity, // Initially all devices are available
                'borrowed_quantity' => 0,
                'is_active' => true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $device->load(['createdBy', 'updatedBy']);

            // Clear cache when device is created
            Cache::forget('available_devices');

            Log::info('Device created successfully', [
                'device_id' => $device->id,
                'device_name' => $device->device_name,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'data' => $device->display_info,
                'message' => 'Device created successfully'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating device: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating device',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified device.
     */
    public function show(DeviceInventory $deviceInventory): JsonResponse
    {
        try {
            $deviceInventory->load(['createdBy', 'updatedBy']);

            return response()->json([
                'success' => true,
                'data' => $deviceInventory->display_info,
                'message' => 'Device retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving device: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving device',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified device.
     */
    public function update(Request $request, DeviceInventory $deviceInventory): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'total_quantity' => 'required|integer|min:0|max:10000',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if total quantity is being reduced below borrowed quantity
            if ($request->total_quantity < $deviceInventory->borrowed_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reduce total quantity below currently borrowed quantity (' . $deviceInventory->borrowed_quantity . ')',
                ], 422);
            }

            $oldTotalQuantity = $deviceInventory->total_quantity;

            $deviceInventory->update([
                'device_name' => $request->device_name,
                'description' => $request->description,
                'is_active' => $request->get('is_active', $deviceInventory->is_active),
                'updated_by' => Auth::id(),
            ]);

            // Update total quantity and adjust available quantity
            if ($request->total_quantity != $oldTotalQuantity) {
                $deviceInventory->updateTotalQuantity($request->total_quantity);
            }

            $deviceInventory->load(['createdBy', 'updatedBy']);

            // Clear cache when device is updated
            Cache::forget('available_devices');

            Log::info('Device updated successfully', [
                'device_id' => $deviceInventory->id,
                'device_name' => $deviceInventory->device_name,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'data' => $deviceInventory->display_info,
                'message' => 'Device updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating device: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating device',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified device.
     */
    public function destroy(DeviceInventory $deviceInventory): JsonResponse
    {
        try {
            // Check if device has unreturned bookings (more accurate than borrowed_quantity)
            $unreturnedBookings = \App\Models\BookingService::where('device_inventory_id', $deviceInventory->id)
                ->whereIn('return_status', ['not_yet_returned'])
                ->whereIn('ict_approve', ['approved']) // Only check approved bookings
                ->count();
                
            if ($unreturnedBookings > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete device with {$unreturnedBookings} unreturned item(s). Please wait for all items to be returned.",
                ], 422);
            }

            // Check if device has associated booking records
            $hasBookings = $deviceInventory->bookingServices()->exists();
            if ($hasBookings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete device with existing booking records. Consider deactivating instead.',
                ], 422);
            }

            $deviceName = $deviceInventory->device_name;
            $deviceInventory->delete();

            // Clear cache when device is deleted
            Cache::forget('available_devices');

            Log::info('Device deleted successfully', [
                'device_name' => $deviceName,
                'deleted_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Device deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting device: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting device',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get devices available for booking.
     */
    public function getAvailableDevices(): JsonResponse
    {
        try {
            // Cache the available devices for 5 minutes to improve performance
            $devices = Cache::remember('available_devices', 300, function () {
                return DeviceInventory::select([
                    'id',
                    'device_name',
                    'device_code',
                    'description',
                    'available_quantity',
                    'total_quantity',
                    'is_active'
                ])
                ->where('is_active', true)
                ->orderBy('device_name')
                ->get()
                ->map(function ($device) {
                    // Calculate availability status efficiently
                    $availabilityStatus = 'available';
                    if ($device->available_quantity === 0) {
                        $availabilityStatus = 'out_of_stock';
                    } elseif ($device->available_quantity <= ($device->total_quantity * 0.2)) {
                        $availabilityStatus = 'low_stock';
                    }
                    
                    return [
                        'id' => $device->id,
                        'device_name' => $device->device_name,
                        'device_code' => $device->device_code,
                        'description' => $device->description,
                        'available_quantity' => $device->available_quantity,
                        'total_quantity' => $device->total_quantity,
                        'can_borrow' => $device->is_active && $device->available_quantity > 0,
                        'availability_status' => $availabilityStatus,
                    ];
                });
            });

            return response()->json([
                'success' => true,
                'data' => $devices,
                'message' => 'Available devices retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving available devices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving available devices',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get device statistics.
     */
    public function getStatistics(): JsonResponse
    {
        try {
            // Calculate the correct statistics efficiently
            $totalInventory = DeviceInventory::active()->sum('total_quantity');
            $availableInventory = DeviceInventory::active()->sum('available_quantity');
            $borrowedInventory = DeviceInventory::active()->sum('borrowed_quantity');
            
            $stats = [
                // Device type counts
                'device_types_count' => DeviceInventory::count(),
                'active_device_types' => DeviceInventory::active()->count(),
                'inactive_device_types' => DeviceInventory::where('is_active', false)->count(),
                
                // Inventory quantities (what the frontend expects)
                'total_devices' => $totalInventory, // Total quantity of all devices
                'available_inventory' => $availableInventory, // Available quantity
                'borrowed_inventory' => $borrowedInventory, // Borrowed quantity
                
                // Device status counts
                'out_of_stock_devices' => DeviceInventory::active()->where('available_quantity', 0)->count(),
                'low_stock_devices' => DeviceInventory::active()
                    ->whereRaw('available_quantity <= (total_quantity * 0.2)')
                    ->where('available_quantity', '>', 0)
                    ->count(),
            ];

            // Verify the formula: Available = Total - Borrowed
            $calculatedAvailable = $totalInventory - $borrowedInventory;
            if ($calculatedAvailable !== $availableInventory) {
                Log::warning('Device inventory calculation mismatch', [
                    'total_inventory' => $totalInventory,
                    'borrowed_inventory' => $borrowedInventory,
                    'calculated_available' => $calculatedAvailable,
                    'actual_available' => $availableInventory
                ]);
            }

            // Calculate overall utilization
            $stats['overall_utilization'] = $totalInventory > 0 
                ? round(($borrowedInventory / $totalInventory) * 100, 2)
                : 0;

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
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Generate a unique device code.
     */
    private function generateDeviceCode(string $deviceName): string
    {
        $baseCode = Str::upper(Str::slug($deviceName, ''));
        $baseCode = substr($baseCode, 0, 10); // Limit to 10 characters
        
        $counter = 1;
        $deviceCode = $baseCode;
        
        while (DeviceInventory::where('device_code', $deviceCode)->exists()) {
            $deviceCode = $baseCode . $counter;
            $counter++;
        }
        
        return $deviceCode;
    }

    /**
     * Get aggregated return status information for a device.
     */
    private function getDeviceReturnStatusInfo(int $deviceId): array
    {
        $bookings = \App\Models\BookingService::where('device_inventory_id', $deviceId)
            ->whereIn('ict_approve', ['approved']) // Only count approved bookings
            ->get();
            
        $totalBookings = $bookings->count();
        $returnedCount = $bookings->where('return_status', 'returned')->count();
        $compromisedCount = $bookings->where('return_status', 'returned_but_compromised')->count();
        $unreturnedCount = $bookings->where('return_status', 'not_yet_returned')->count();
        
        // Determine summary status
        $summary = 'no_bookings';
        if ($totalBookings > 0) {
            if ($compromisedCount > 0) {
                $summary = 'some_compromised';
            } elseif ($unreturnedCount === 0) {
                $summary = 'all_returned';
            } elseif ($returnedCount > 0) {
                $summary = 'partially_returned';
            } else {
                $summary = 'none_returned';
            }
        }
        
        return [
            'summary' => $summary,
            'total_bookings' => $totalBookings,
            'returned_count' => $returnedCount,
            'compromised_count' => $compromisedCount,
            'unreturned_count' => $unreturnedCount,
        ];
    }

    /**
     * Fix device inventory quantity inconsistencies.
     */
    public function fixQuantities(): JsonResponse
    {
        try {
            $devices = DeviceInventory::all();
            $fixedCount = 0;
            $fixedDevices = [];
            
            foreach ($devices as $device) {
                $expectedAvailable = $device->total_quantity - $device->borrowed_quantity;
                
                if ($device->available_quantity !== $expectedAvailable) {
                    $fixedDevices[] = [
                        'device_name' => $device->device_name,
                        'total_quantity' => $device->total_quantity,
                        'borrowed_quantity' => $device->borrowed_quantity,
                        'old_available_quantity' => $device->available_quantity,
                        'new_available_quantity' => max(0, $expectedAvailable)
                    ];
                    
                    $device->available_quantity = max(0, $expectedAvailable);
                    $device->save();
                    
                    $fixedCount++;
                }
            }
            
            Log::info('Device inventory quantities fixed', [
                'fixed_count' => $fixedCount,
                'fixed_devices' => $fixedDevices,
                'fixed_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'fixed_count' => $fixedCount,
                    'fixed_devices' => $fixedDevices
                ],
                'message' => $fixedCount > 0 
                    ? "Fixed {$fixedCount} device(s) with quantity inconsistencies"
                    : 'All device quantities are already consistent'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fixing device quantities: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fixing device quantities',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}