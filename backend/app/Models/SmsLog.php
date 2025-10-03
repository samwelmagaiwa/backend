<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'message',
        'type',
        'status',
        'provider_response',
        'sent_at',
        'user_id',
        'reference_id',
        'reference_type'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'provider_response' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user associated with this SMS log (if any)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the polymorphic relation for reference (request, announcement, etc.)
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Scope for successful SMS
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for failed SMS
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for specific SMS type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for SMS sent within date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted phone number for display
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = $this->phone_number;
        
        // Format Tanzanian numbers for display
        if (strlen($phone) === 12 && substr($phone, 0, 3) === '255') {
            return '+255 ' . substr($phone, 3, 2) . ' ' . substr($phone, 5);
        }
        
        return $phone;
    }

    /**
     * Get success status as boolean
     */
    public function getIsSuccessfulAttribute()
    {
        return $this->status === 'sent';
    }

    /**
     * Get readable status
     */
    public function getReadableStatusAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Get short message for display
     */
    public function getShortMessageAttribute()
    {
        return strlen($this->message) > 50 
            ? substr($this->message, 0, 50) . '...' 
            : $this->message;
    }

    /**
     * Get delivery status from provider response
     */
    public function getDeliveryStatusAttribute()
    {
        if (!is_array($this->provider_response)) {
            return 'Unknown';
        }

        // Common provider response keys
        $statusKeys = ['status', 'delivery_status', 'state'];
        
        foreach ($statusKeys as $key) {
            if (isset($this->provider_response[$key])) {
                return $this->provider_response[$key];
            }
        }

        return $this->status === 'sent' ? 'Sent' : 'Failed';
    }
}