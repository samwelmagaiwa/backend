<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'permissions',
        'is_system_role',
        'is_deletable',
        'sort_order'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system_role' => 'boolean',
        'is_deletable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Users with this role (many-to-many relationship)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withPivot(['assigned_at', 'assigned_by'])
            ->withTimestamps();
    }

    /**
     * Role change logs
     */
    public function changeLogs(): HasMany
    {
        return $this->hasMany(RoleChangeLog::class);
    }

    /**
     * Scope for system roles
     */
    public function scopeSystemRoles($query)
    {
        return $query->where('is_system_role', true);
    }

    /**
     * Scope for deletable roles
     */
    public function scopeDeletable($query)
    {
        return $query->where('is_deletable', true);
    }

    /**
     * Scope for non-system roles
     */
    public function scopeCustomRoles($query)
    {
        return $query->where('is_system_role', false);
    }

    /**
     * Check if role can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->is_deletable && !$this->is_system_role;
    }

    /**
     * Check if role has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Get role display name with user count
     */
    public function getDisplayNameAttribute(): string
    {
        $userCount = $this->users()->count();
        return "{$this->name} ({$userCount} users)";
    }

    /**
     * Boot method to set default sort order
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            if (is_null($role->sort_order)) {
                $role->sort_order = static::max('sort_order') + 1;
            }
        });
    }
}