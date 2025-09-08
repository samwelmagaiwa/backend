<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceInventory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'device_inventory';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'device_name',
        'device_code',
        'description',
        'total_quantity',
        'available_quantity',
        'borrowed_quantity',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_quantity' => 'integer',
        'available_quantity' => 'integer',
        'borrowed_quantity' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this device.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this device.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the booking services for this device.
     */
    public function bookingServices(): HasMany
    {
        return $this->hasMany(BookingService::class, 'device_inventory_id');
    }

    /**
     * Scope a query to only include active devices.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include available devices.
     */
    public function scopeAvailable($query)
    {
        return $query->where('available_quantity', '>', 0);
    }

    /**
     * Check if device is available for borrowing.
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->available_quantity > 0;
    }

    /**
     * Borrow a device (decrease available quantity).
     */
    public function borrowDevice(int $quantity = 1): bool
    {
        if ($this->available_quantity >= $quantity) {
            $this->available_quantity -= $quantity;
            $this->borrowed_quantity += $quantity;
            return $this->save();
        }
        return false;
    }

    /**
     * Return a device (increase available quantity).
     */
    public function returnDevice(int $quantity = 1): bool
    {
        if ($this->borrowed_quantity >= $quantity) {
            $this->available_quantity += $quantity;
            $this->borrowed_quantity -= $quantity;
            return $this->save();
        }
        return false;
    }

    /**
     * Update total quantity and adjust available quantity.
     */
    public function updateTotalQuantity(int $newTotal): bool
    {
        $difference = $newTotal - $this->total_quantity;
        
        $this->total_quantity = $newTotal;
        $this->available_quantity += $difference;
        
        // Ensure available quantity doesn't go negative
        if ($this->available_quantity < 0) {
            $this->available_quantity = 0;
        }
        
        return $this->save();
    }

    /**
     * Get the utilization percentage.
     */
    public function getUtilizationPercentageAttribute(): float
    {
        if ($this->total_quantity === 0) {
            return 0;
        }
        
        return round(($this->borrowed_quantity / $this->total_quantity) * 100, 2);
    }

    /**
     * Get the availability status.
     */
    public function getAvailabilityStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        
        if ($this->available_quantity === 0) {
            return 'out_of_stock';
        }
        
        if ($this->available_quantity <= ($this->total_quantity * 0.2)) {
            return 'low_stock';
        }
        
        return 'available';
    }

    /**
     * Verify and fix quantity consistency.
     * Ensures: available_quantity = total_quantity - borrowed_quantity
     */
    public function verifyAndFixQuantities(): bool
    {
        $expectedAvailable = $this->total_quantity - $this->borrowed_quantity;
        
        if ($this->available_quantity !== $expectedAvailable) {
            \Log::warning('Device quantity inconsistency detected and fixed', [
                'device_id' => $this->id,
                'device_name' => $this->device_name,
                'total_quantity' => $this->total_quantity,
                'borrowed_quantity' => $this->borrowed_quantity,
                'old_available_quantity' => $this->available_quantity,
                'new_available_quantity' => $expectedAvailable
            ]);
            
            $this->available_quantity = max(0, $expectedAvailable);
            return $this->save();
        }
        
        return true;
    }

    /**
     * Get formatted device info for display.
     */
    public function getDisplayInfoAttribute(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->device_name,
            'code' => $this->device_code,
            'description' => $this->description,
            'total' => $this->total_quantity,
            'available' => $this->available_quantity,
            'borrowed' => $this->borrowed_quantity,
            'utilization' => $this->utilization_percentage,
            'status' => $this->availability_status,
            'is_active' => $this->is_active,
            'can_borrow' => $this->isAvailable(),
        ];
    }
}