<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WellsoftModule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'wellsoft_modules';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
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
     * Get the user access requests that have selected this module.
     */
    public function userAccessRequests(): BelongsToMany
    {
        return $this->belongsToMany(UserAccess::class, 'wellsoft_modules_selected', 'module_id', 'user_access_id')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active modules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive modules.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Get the module's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Check if the module is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Activate the module.
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the module.
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Get all active modules as options for forms.
     */
    public static function getActiveOptions(): array
    {
        return static::active()
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray();
    }

    /**
     * Get all active modules as a simple array.
     */
    public static function getActiveModuleNames(): array
    {
        return static::active()
                    ->orderBy('name')
                    ->pluck('name')
                    ->toArray();
    }
}