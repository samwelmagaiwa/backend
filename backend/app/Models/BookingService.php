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
        'department',
        'phone_number',
        'return_date',
        'return_time',
        'reason',
        'signature_path',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
        'device_collected_at',
        'device_returned_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'booking_date' => 'date',
        'return_date' => 'date',
        'return_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
        'device_collected_at' => 'datetime',
        'device_returned_at' => 'datetime',
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
     * Get the department information.
     */
    public function departmentInfo(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department', 'id');
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
}