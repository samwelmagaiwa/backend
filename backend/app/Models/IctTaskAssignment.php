<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IctTaskAssignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_access_id',
        'assigned_by_user_id',
        'ict_officer_user_id',
        'status',
        'assignment_notes',
        'progress_notes',
        'completion_notes',
        'assigned_at',
        'started_at',
        'completed_at',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_ASSIGNED => 'Assigned',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    /**
     * Get the user access request.
     */
    public function userAccess(): BelongsTo
    {
        return $this->belongsTo(UserAccess::class);
    }

    /**
     * Get the user who assigned the task (Head of IT).
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    /**
     * Get the ICT officer assigned to the task.
     */
    public function ictOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ict_officer_user_id');
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by ICT officer.
     */
    public function scopeForIctOfficer($query, int $ictOfficerId)
    {
        return $query->where('ict_officer_user_id', $ictOfficerId);
    }

    /**
     * Scope for filtering by assigner.
     */
    public function scopeAssignedBy($query, int $assignerId)
    {
        return $query->where('assigned_by_user_id', $assignerId);
    }

    /**
     * Check if the task is assigned.
     */
    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    /**
     * Check if the task is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if the task is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the task is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
