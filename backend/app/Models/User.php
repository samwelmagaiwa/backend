<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method string createToken(string $name, array $abilities = [])
 */

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'pf_number',
        'staff_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function onboarding()
    {
        return $this->hasOne(UserOnboarding::class);
    }

    public function declaration()
    {
        return $this->hasOne(Declaration::class);
    }

    /**
     * Get the departments where this user is the head of department.
     */
    public function departmentsAsHOD()
    {
        return $this->hasMany(Department::class, 'hod_user_id');
    }

    /**
     * Roles assigned to this user (many-to-many relationship)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot(['assigned_at', 'assigned_by'])
            ->withTimestamps();
    }

    /**
     * Role changes made by this user
     */
    public function roleChanges()
    {
        return $this->hasMany(RoleChangeLog::class, 'changed_by');
    }

    /**
     * Role changes for this user
     */
    public function roleHistory()
    {
        return $this->hasMany(RoleChangeLog::class, 'user_id');
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles(array $roleNames): bool
    {
        $userRoles = $this->roles()->pluck('name')->toArray();
        return empty(array_diff($roleNames, $userRoles));
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles()->get()->some(function ($role) use ($permission) {
            return $role->hasPermission($permission);
        });
    }

    /**
     * Get all permissions for this user
     */
    public function getAllPermissions(): array
    {
        return $this->roles()->get()
            ->pluck('permissions')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Check if user is admin (use old system)
     */
    public function isAdmin(): bool
    {
        // Prioritize old single role system
        if ($this->role_id && $this->role) {
            return $this->role->name === 'admin';
        }
        
        // Fallback to new system
        return $this->hasRole('admin');
    }

    /**
     * Check if user is super admin (use old system)
     */
    public function isSuperAdmin(): bool
    {
        // Prioritize old single role system
        if ($this->role_id && $this->role) {
            return $this->role->name === 'super_admin';
        }
        
        // Fallback to new system
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is HOD (use old system)
     */
    public function isHOD(): bool
    {
        // Prioritize old single role system
        if ($this->role_id && $this->role) {
            return $this->role->name === 'head_of_department';
        }
        
        // Fallback to new system
        return $this->hasRole('head_of_department');
    }

    /**
     * Get user's primary role (prioritize old single role system)
     */
    public function getPrimaryRole()
    {
        // Prioritize old single role system (as it was yesterday)
        if ($this->role_id && $this->role) {
            return $this->role;
        }
        
        // Fallback to new many-to-many system if old system not available
        return $this->roles()->orderBy('sort_order')->first();
    }

    /**
     * Get role names as array (prioritize old system)
     */
    public function getRoleNamesAttribute(): array
    {
        // Prioritize old single role system
        if ($this->role_id && $this->role) {
            return [$this->role->name];
        }
        
        // Fallback to new many-to-many system
        return $this->roles()->pluck('name')->toArray();
    }

    /**
     * Get primary role name for backward compatibility (use old system)
     */
    public function getPrimaryRoleName(): ?string
    {
        // Prioritize old single role system (as it was yesterday)
        if ($this->role_id && $this->role) {
            return $this->role->name;
        }
        
        // Fallback to new many-to-many system
        return $this->getPrimaryRole()?->name;
    }

    /**
     * Check if user has role using either old or new system (prioritize old)
     */
    public function hasRoleCompat(string $roleName): bool
    {
        // Check old single role system first (as it was yesterday)
        if ($this->role_id && $this->role && $this->role->name === $roleName) {
            return true;
        }
        
        // Fallback to new many-to-many system
        if ($this->hasRole($roleName)) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if user needs onboarding (non-admin users who haven't completed it)
     */
    public function needsOnboarding(): bool
    {
        // Admin users don't need onboarding
        if ($this->isAdmin() || $this->isSuperAdmin()) {
            return false;
        }

        // Check if onboarding record exists and is completed
        $onboarding = $this->onboarding;
        return !$onboarding || !$onboarding->completed;
    }

    /**
     * Get or create onboarding record for user
     */
    public function getOrCreateOnboarding(): UserOnboarding
    {
        return $this->onboarding()->firstOrCreate(
            ['user_id' => $this->id],
            ['current_step' => 'terms-popup']
        );
    }
}

    
