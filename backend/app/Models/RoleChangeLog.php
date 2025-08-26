<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleChangeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_id',
        'action',
        'changed_by',
        'metadata',
        'changed_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * User who had role changed
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Role that was changed
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * User who made the change
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Scope for recent changes
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('changed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for specific action
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Get formatted action text
     */
    public function getActionTextAttribute(): string
    {
        return match($this->action) {
            'assigned' => 'Role Assigned',
            'removed' => 'Role Removed',
            default => ucfirst($this->action)
        };
    }
}