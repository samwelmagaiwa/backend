<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'hod_user_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the users that belong to this department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the user access requests for the department.
     */
    public function userAccessRequests(): HasMany
    {
        return $this->hasMany(UserAccess::class);
    }

    /**
     * Get the head of department user.
     */
    public function headOfDepartment()
    {
        return $this->belongsTo(User::class, 'hod_user_id');
    }

    /**
     * Scope a query to only include active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the department's full name with code.
     */
    public function getFullNameAttribute(): string
    {
        return $this->code ? "{$this->name} ({$this->code})" : $this->name;
    }
}