<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOnboarding extends Model
{
    protected $table = 'user_onboarding';

    protected $fillable = [
        'user_id',
        'terms_accepted',
        'terms_accepted_at',
        'ict_policy_accepted',
        'ict_policy_accepted_at',
        'declaration_submitted',
        'declaration_submitted_at',
        'declaration_data',
        'completed',
        'completed_at',
        'current_step',
    ];

    protected $casts = [
        'terms_accepted' => 'boolean',
        'terms_accepted_at' => 'datetime',
        'ict_policy_accepted' => 'boolean',
        'ict_policy_accepted_at' => 'datetime',
        'declaration_submitted' => 'boolean',
        'declaration_submitted_at' => 'datetime',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'declaration_data' => 'array',
    ];

    /**
     * Get the user that owns the onboarding record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user needs onboarding
     */
    public function needsOnboarding(): bool
    {
        return !$this->completed;
    }

    /**
     * Get the next step in the onboarding process
     */
    public function getNextStep(): string
    {
        if (!$this->terms_accepted) {
            return 'terms-popup';
        }
        
        if (!$this->ict_policy_accepted) {
            return 'policy-popup';
        }
        
        if (!$this->declaration_submitted) {
            return 'declaration';
        }
        
        return 'success';
    }

    /**
     * Mark terms as accepted
     */
    public function acceptTerms(): void
    {
        $this->update([
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
            'current_step' => 'policy-popup', // Move to ICT policy step
        ]);
    }

    /**
     * Mark ICT policy as accepted
     */
    public function acceptIctPolicy(): void
    {
        $this->update([
            'ict_policy_accepted' => true,
            'ict_policy_accepted_at' => now(),
            'current_step' => 'declaration', // Move to declaration step
        ]);
    }

    /**
     * Submit declaration form
     */
    public function submitDeclaration(array $declarationData): void
    {
        $this->update([
            'declaration_submitted' => true,
            'declaration_submitted_at' => now(),
            'declaration_data' => $declarationData,
            'current_step' => 'success', // Move to success step after declaration
        ]);
    }

    /**
     * Complete the onboarding process
     */
    public function complete(): void
    {
        $this->update([
            'completed' => true,
            'completed_at' => now(),
            'current_step' => 'completed',
        ]);
    }

    /**
     * Reset onboarding progress
     */
    public function reset(string $type = 'all'): void
    {
        $updates = ['current_step' => 'terms-popup'];

        switch ($type) {
            case 'terms':
                $updates = array_merge($updates, [
                    'terms_accepted' => false,
                    'terms_accepted_at' => null,
                    'ict_policy_accepted' => false,
                    'ict_policy_accepted_at' => null,
                    'declaration_submitted' => false,
                    'declaration_submitted_at' => null,
                    'declaration_data' => null,
                    'completed' => false,
                    'completed_at' => null,
                ]);
                break;
                
            case 'ict':
                $updates = array_merge($updates, [
                    'ict_policy_accepted' => false,
                    'ict_policy_accepted_at' => null,
                    'declaration_submitted' => false,
                    'declaration_submitted_at' => null,
                    'declaration_data' => null,
                    'completed' => false,
                    'completed_at' => null,
                    'current_step' => 'policy-popup',
                ]);
                break;
                
            case 'all':
            default:
                $updates = array_merge($updates, [
                    'terms_accepted' => false,
                    'terms_accepted_at' => null,
                    'ict_policy_accepted' => false,
                    'ict_policy_accepted_at' => null,
                    'declaration_submitted' => false,
                    'declaration_submitted_at' => null,
                    'declaration_data' => null,
                    'completed' => false,
                    'completed_at' => null,
                ]);
                break;
        }

        $this->update($updates);
    }
}