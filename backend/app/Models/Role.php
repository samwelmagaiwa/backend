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
     * Get role hierarchy priority (higher number = higher priority)
     */
    public function getPriority(): int
    {
        return $this->getRolePriority($this->name);
    }

    /**
     * Get role priority based on role name
     */
    public static function getRolePriority(string $roleName): int
    {
        $hierarchy = [
            'admin' => 100,
            'ict_director' => 90,
            'dict' => 90, // Legacy support
            'divisional_director' => 80,
            'head_of_department' => 70,
            'head_of_it' => 65,
            'ict_officer' => 60,
            'hod_it' => 55, // Legacy support
            'staff' => 10,
        ];

        return $hierarchy[$roleName] ?? 5; // Default priority for unknown roles
    }

    /**
     * Check if this role has higher priority than another role
     */
    public function hasHigherPriorityThan(Role $otherRole): bool
    {
        return $this->getPriority() > $otherRole->getPriority();
    }

    /**
     * Get the highest priority role from a collection of roles
     */
    public static function getHighestPriorityRole($roles)
    {
        if (empty($roles) || $roles->isEmpty()) {
            return null;
        }

        return $roles->sortByDesc(function ($role) {
            return $role->getPriority();
        })->first();
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