<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BookingService extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'booking_service';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'booking_date',
        'borrower_name',
        'device_type',
        'custom_device',
        'device_inventory_id', // Link to device inventory
        'department',
        'phone_number',
        'return_date',
        'return_time',
        'return_date_time', // Combined return date and time
        'reason',
        'signature_path',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
        'device_collected_at',
        'device_returned_at',
        // ICT Approval fields
        'ict_approve',
        'ict_approved_by',
        'ict_approved_at',
        'ict_notes',
        // Return Status field
        'return_status',
        // Device condition assessment fields
        'device_condition_receiving',
        'device_condition_issuing',
        'device_received_at',
        'device_issued_at',
        'assessed_by',
        'assessment_notes',
        // SMS notification tracking
        'sms_notifications',
        'sms_sent_to_hod_at',
        'sms_to_hod_status',
        'sms_sent_to_divisional_at',
        'sms_to_divisional_status',
        'sms_sent_to_ict_director_at',
        'sms_to_ict_director_status',
        'sms_sent_to_head_it_at',
        'sms_to_head_it_status',
        'sms_sent_to_requester_at',
        'sms_to_requester_status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'booking_date' => 'date',
        'return_date' => 'date',
        'return_time' => 'datetime:H:i',
        'return_date_time' => 'datetime',
        'approved_at' => 'datetime',
        'device_collected_at' => 'datetime',
        'device_returned_at' => 'datetime',
        'ict_approved_at' => 'datetime',
        'device_received_at' => 'datetime',
        'device_issued_at' => 'datetime',
        'device_condition_receiving' => 'array',
        'device_condition_issuing' => 'array',
    ];

    /**
     * Available device types
     */
    public static function getDeviceTypes(): array
    {
        // Get device types from actual inventory and map to enum-compatible keys
        try {
            $inventoryDevices = [];
            $devices = \App\Models\DeviceInventory::distinct()->pluck('device_name');
            
            foreach ($devices as $deviceName) {
                $normalizedName = strtoupper($deviceName);
                
                // Map inventory device names to enum values
                switch ($normalizedName) {
                    case 'PROJECTOR':
                        $inventoryDevices['projector'] = 'Projector';
                        break;
                    case 'HDMI':
                        $inventoryDevices['hdmi_cable'] = 'HDMI Cable';
                        break;
                    case 'TV':
                    case 'TV REMOTE':
                        $inventoryDevices['tv_remote'] = 'TV Remote';
                        break;
                    case 'MONITOR':
                        $inventoryDevices['monitor'] = 'Monitor';
                        break;
                    case 'CPU':
                        $inventoryDevices['cpu'] = 'CPU';
                        break;
                    case 'KEYBOARD':
                        $inventoryDevices['keyboard'] = 'Keyboard';
                        break;
                    case 'PC':
                        $inventoryDevices['pc'] = 'PC';
                        break;
                    default:
                        // For unknown devices, use 'others' and let user specify
                        break;
                }
            }
            
            // Always include 'others' option for custom devices
            $inventoryDevices['others'] = 'Others (Specify Below)';
            
            return $inventoryDevices;
        } catch (\Exception $e) {
            // Fallback to enum-compatible static list
            return [
                'projector' => 'Projector',
                'hdmi_cable' => 'HDMI Cable',
                'others' => 'Others (Specify Below)'
            ];
        }
    }

    /**
     * Available status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'in_use' => 'Device In Use',
            'returned' => 'Device Returned',
            'overdue' => 'Overdue'
        ];
    }

    /**
     * Available return status options
     */
    public static function getReturnStatusOptions(): array
    {
        return [
            'not_yet_returned' => 'Not Yet Returned',
            'returned' => 'Returned',
            'returned_but_compromised' => 'Returned but Compromised'
        ];
    }

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the booking.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the ICT officer who approved the booking.
     */
    public function ictApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ict_approved_by');
    }

    /**
     * Get the department information.
     */
    public function departmentInfo(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department', 'id');
    }

    /**
     * Get the device inventory information.
     */
    public function deviceInventory(): BelongsTo
    {
        return $this->belongsTo(DeviceInventory::class, 'device_inventory_id');
    }

    /**
     * Get the ICT officer who performed the assessment.
     */
    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    /**
     * Get the department name.
     */
    public function getDepartmentNameAttribute(): string
    {
        return $this->departmentInfo ? $this->departmentInfo->name : 'Unknown Department';
    }

    /**
     * Get the device display name.
     */
    public function getDeviceDisplayNameAttribute(): string
    {
        // Handle custom devices (both old 'other' and new 'others')
        if (in_array($this->device_type, ['others', 'other']) && $this->custom_device) {
            return $this->custom_device;
        }
        
        // Get current device types
        $deviceTypes = self::getDeviceTypes();
        
        // Check current device types first
        if (isset($deviceTypes[$this->device_type])) {
            return $deviceTypes[$this->device_type];
        }
        
        // Backward compatibility for old device types
        $legacyDeviceTypes = [
            'tv_remote' => 'TV Remote',
            'hdmi_cable' => 'HDMI Cable',
            'monitor' => 'Monitor',
            'cpu' => 'CPU',
            'keyboard' => 'Keyboard',
            'pc' => 'PC',
            'other' => 'Other Device' // Legacy support
        ];
        
        return $legacyDeviceTypes[$this->device_type] ?? ucwords(str_replace('_', ' ', $this->device_type));
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayNameAttribute(): string
    {
        $statusOptions = self::getStatusOptions();
        return $statusOptions[$this->status] ?? $this->status;
    }

    /**
     * Get the return status display name.
     */
    public function getReturnStatusDisplayNameAttribute(): string
    {
        $returnStatusOptions = self::getReturnStatusOptions();
        return $returnStatusOptions[$this->return_status] ?? $this->return_status;
    }

    /**
     * Check if the booking is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'returned') {
            return false;
        }

        $returnDateTime = Carbon::parse($this->return_date->format('Y-m-d') . ' ' . $this->return_time);
        return $returnDateTime->isPast() && in_array($this->status, ['approved', 'in_use']);
    }

    /**
     * Get the signature URL.
     */
    public function getSignatureUrlAttribute(): ?string
    {
        if (!$this->signature_path) {
            return null;
        }

        return asset('storage/' . $this->signature_path);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by device type.
     */
    public function scopeByDeviceType($query, string $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Scope for filtering by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('booking_date', [$startDate, $endDate]);
    }

    /**
     * Scope for overdue bookings.
     */
    public function scopeOverdue($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'approved')
              ->orWhere('status', 'in_use');
        })->whereRaw("CONCAT(return_date, ' ', return_time) < NOW()");
    }

    /**
     * Scope for user's bookings.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by ICT approval status.
     */
    public function scopeByIctApprovalStatus($query, string $ictApprove)
    {
        return $query->where('ict_approve', $ictApprove);
    }

    /**
     * Scope for pending ICT approval.
     */
    public function scopePendingIctApproval($query)
    {
        return $query->where('ict_approve', 'pending');
    }

    /**
     * Get the nearest upcoming return date for a specific device inventory.
     * Used when device is out of stock to inform next requester.
     */
    public static function getNearestReturnDateTime(int $deviceInventoryId): ?Carbon
    {
        $booking = self::where('device_inventory_id', $deviceInventoryId)
            ->whereIn('status', ['approved', 'in_use'])
            ->whereNotNull('return_date_time')
            ->where('return_date_time', '>', now())
            ->orderBy('return_date_time', 'asc')
            ->first();

        return $booking ? $booking->return_date_time : null;
    }

    /**
     * Get all active bookings for a device (approved or in_use).
     */
    public static function getActiveBookingsForDevice(int $deviceInventoryId)
    {
        return self::where('device_inventory_id', $deviceInventoryId)
            ->whereIn('status', ['approved', 'in_use'])
            ->with(['user'])
            ->orderBy('return_date_time', 'asc')
            ->get();
    }

    /**
     * Check if device is available for borrowing.
     * Returns array with availability status and message.
     */
    public static function checkDeviceAvailability(int $deviceInventoryId): array
    {
        $deviceInventory = DeviceInventory::find($deviceInventoryId);
        
        if (!$deviceInventory) {
            return [
                'available' => false,
                'message' => 'Device not found in inventory.',
                'can_request' => false
            ];
        }

        if (!$deviceInventory->is_active) {
            return [
                'available' => false,
                'message' => 'Device is currently inactive.',
                'can_request' => false
            ];
        }

        if ($deviceInventory->available_quantity > 0) {
            return [
                'available' => true,
                'message' => 'Device is available for borrowing.',
                'can_request' => true,
                'available_quantity' => $deviceInventory->available_quantity
            ];
        }

        // Device is out of stock, check for nearest return
        $nearestReturn = self::getNearestReturnDateTime($deviceInventoryId);
        
        if ($nearestReturn) {
            return [
                'available' => false,
                'message' => "Device is currently in use by another staff. Kindly check your request after {$nearestReturn->format('M d, Y \\a\\t g:i A')}.",
                'can_request' => true,
                'nearest_return' => $nearestReturn,
                'available_quantity' => 0
            ];
        }

        return [
            'available' => false,
            'message' => 'Device is currently unavailable with no scheduled return.',
            'can_request' => true,
            'available_quantity' => 0
        ];
    }

    /**
     * Check if user has any active booking requests.
     * Returns the active request if found, null otherwise.
     */
    public static function getUserActiveRequest(int $userId): ?self
    {
        return self::where('user_id', $userId)
            ->where(function ($query) {
                $query->where(function ($q) {
                    // Pending requests (not yet processed by ICT)
                    $q->where('status', 'pending')
                      ->where('ict_approve', 'pending');
                })
                ->orWhere(function ($q) {
                    // Approved requests (approved by ICT but not yet returned)
                    $q->whereIn('status', ['approved', 'in_use'])
                      ->where('ict_approve', 'approved');
                })
                ->orWhere(function ($q) {
                    // Returned status but device not yet returned (status should be 'returned' but return_status is 'not_yet_returned')
                    $q->where('ict_approve', 'approved')
                      ->where('return_status', 'not_yet_returned');
                });
            })
            ->first();
    }

    /**
     * Check if user can submit a new booking request.
     * Returns array with status and message.
     */
    public static function canUserSubmitNewRequest(int $userId): array
    {
        $activeRequest = self::getUserActiveRequest($userId);
        
        if (!$activeRequest) {
            return [
                'can_submit' => true,
                'message' => 'User can submit a new booking request.',
                'active_request' => null
            ];
        }
        
        // Determine appropriate message based on request status and return status
        $message = '';
        
        if ($activeRequest->status === 'pending' && $activeRequest->ict_approve === 'pending') {
            $message = 'You have a pending booking request. Please wait for it to be processed by ICT before submitting a new request.';
        } elseif ($activeRequest->return_status === 'not_yet_returned' && $activeRequest->ict_approve === 'approved') {
            // User has an approved device that hasn't been returned yet
            $deviceName = $activeRequest->getDeviceDisplayNameAttribute();
            $requestId = $activeRequest->id;
            $message = "You still have a device ({$deviceName}) that has not been returned (Request ID: #{$requestId}). Please return the device before submitting a new request. Contact ICT office for device return process.";
        } elseif ($activeRequest->status === 'approved' || $activeRequest->status === 'in_use') {
            $message = 'You have an active device booking. Please return the current device before requesting a new one.';
        } else {
            $message = 'You have an active request. Please complete the current request first.';
        }
        
        return [
            'can_submit' => false,
            'message' => $message,
            'active_request' => $activeRequest,
            'return_status' => $activeRequest->return_status,
            'request_id' => $activeRequest->id
        ];
    }
}