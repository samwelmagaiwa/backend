<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TaskAssignment extends Model
{
    use SoftDeletes;

    protected $table = 'task_assignments';

    protected $fillable = [
        'request_id',
        'assigned_to',
        'assigned_by',
        'status',
        'assigned_at',
        'completed_at',
        'cancelled_at',
        'cancelled_by',
        'priority',
        'description',
        'progress_notes',
        'estimated_completion',
        'completion_notes',
        'cancellation_reason'
    ];

    protected $dates = [
        'assigned_at',
        'completed_at',
        'cancelled_at',
        'estimated_completion'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'estimated_completion' => 'datetime',
    ];

    // Define the possible status values
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Define priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Get the access request associated with this assignment
     */
    public function accessRequest()
    {
        return $this->belongsTo(UserCombinedAccessRequest::class, 'request_id');
    }

    /**
     * Get the user (ICT Officer) assigned to this task
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user (Head of IT) who assigned this task
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the user who cancelled this task assignment
     */
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Scope to get active assignments
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_ASSIGNED, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Scope to get completed assignments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope to get cancelled assignments
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope to get assignments for a specific officer
     */
    public function scopeForOfficer($query, $officerId)
    {
        return $query->where('assigned_to', $officerId);
    }

    /**
     * Scope to get assignments with specific priority
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Check if assignment is overdue
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->estimated_completion || $this->status === self::STATUS_COMPLETED) {
            return false;
        }

        return $this->estimated_completion->isPast();
    }

    /**
     * Get days until/since estimated completion
     */
    public function getDaysUntilCompletionAttribute()
    {
        if (!$this->estimated_completion) {
            return null;
        }

        return $this->estimated_completion->diffInDays(now(), false);
    }

    /**
     * Get the duration of the assignment in hours
     */
    public function getDurationInHoursAttribute()
    {
        if (!$this->assigned_at) {
            return 0;
        }

        $endTime = $this->completed_at ?? now();
        return $this->assigned_at->diffInHours($endTime);
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($assignment) {
            if (!$assignment->status) {
                $assignment->status = self::STATUS_ASSIGNED;
            }
            
            if (!$assignment->priority) {
                $assignment->priority = self::PRIORITY_NORMAL;
            }

            if (!$assignment->assigned_at) {
                $assignment->assigned_at = now();
            }
        });
    }

    /**
     * Mark assignment as in progress
     */
    public function markInProgress($notes = null)
    {
        $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'progress_notes' => $notes
        ]);

        return $this;
    }

    /**
     * Complete the assignment
     */
    public function complete($notes = null)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'completion_notes' => $notes
        ]);

        return $this;
    }

    /**
     * Cancel the assignment
     */
    public function cancel($reason = null, $cancelledBy = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancelled_by' => $cancelledBy ?? auth()->id(),
            'cancellation_reason' => $reason
        ]);

        return $this;
    }
}
