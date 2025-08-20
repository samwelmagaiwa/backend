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

    /**
     * Check if user needs onboarding (non-admin users who haven't completed it)
     */
    public function needsOnboarding(): bool
    {
        // Admin users don't need onboarding
        if ($this->role && $this->role->name === 'admin') {
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

    
