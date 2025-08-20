<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccess extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'user_access';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'pf_number',
        'staff_name',
        'phone_number',
        'department_id',
        'signature_path',
        'purpose',
        'request_type',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Request type constants
     */
    const REQUEST_TYPES = [
        'jeeva_access' => 'Jeeva Access',
        'wellsoft' => 'Wellsoft',
        'internet_access_request' => 'Internet Access Request',
    ];

    /**
     * Status constants
     */
    const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'in_review' => 'In Review',
    ];

    /**
     * Get the user that owns the access request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that the request belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope a query to only include requests of a given type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('request_type', $type);
    }

    /**
     * Scope a query to only include requests with a given status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the formatted request type name.
     */
    public function getRequestTypeNameAttribute(): string
    {
        return self::REQUEST_TYPES[$this->request_type] ?? $this->request_type;
    }

    /**
     * Get the formatted status name.
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the request is in review.
     */
    public function isInReview(): bool
    {
        return $this->status === 'in_review';
    }
}