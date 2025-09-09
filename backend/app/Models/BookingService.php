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
    ];

    /**
     * Available device types
     */
    public static function getDeviceTypes(): array
    {
        return [
            'projector' => 'Projector',
            'tv_remote' => 'TV Remote',
            'hdmi_cable' => 'HDMI Cable',
            'monitor' => 'Monitor',
            'cpu' => 'CPU',
            'keyboard' => 'Keyboard',
            'pc' => 'PC',
            'others' => 'Others'
        ];
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
        if ($this->device_type === 'others' && $this->custom_device) {
            return $this->custom_device;
        }
        
        $deviceTypes = self::getDeviceTypes();
        return $deviceTypes[$this->device_type] ?? $this->device_type;
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
}