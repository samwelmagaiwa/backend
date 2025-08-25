<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ModuleAccessRequest extends Model
{
    protected $fillable = [
        'user_id',
        'access_type',
        'temporary_until',
        'modules',
        'comments',
        // Personal information fields
        'pf_number',
        'staff_name',
        'phone_number',
        'department_id',
        'signature_path',
        // Form type and services
        'form_type',
        'services_requested',
        // HOD approval fields
        'hod_approval_status',
        'hod_comments',
        'hod_signature_path',
        'hod_approved_at',
        'hod_user_id',
        // Divisional Director approval fields
        'divisional_director_approval_status',
        'divisional_director_comments',
        'divisional_director_signature_path',
        'divisional_director_approved_at',
        'divisional_director_user_id',
        // DICT approval fields
        'dict_approval_status',
        'dict_comments',
        'dict_signature_path',
        'dict_approved_at',
        'dict_user_id',
        // Head of IT approval fields
        'hod_it_approval_status',
        'hod_it_comments',
        'hod_it_signature_path',
        'hod_it_approved_at',
        'hod_it_user_id',
        // ICT Officer approval fields
        'ict_officer_approval_status',
        'ict_officer_comments',
        'ict_officer_signature_path',
        'ict_officer_approved_at',
        'ict_officer_user_id',
        // Overall status
        'overall_status',
        'current_approval_stage',
    ];

    protected $casts = [
        'modules' => 'array',
        'services_requested' => 'array',
        'temporary_until' => 'date',
        'hod_approved_at' => 'datetime',
        'divisional_director_approved_at' => 'datetime',
        'dict_approved_at' => 'datetime',
        'hod_it_approved_at' => 'datetime',
        'ict_officer_approved_at' => 'datetime',
    ];

    // Form type constants
    const FORM_TYPE_MODULE_ACCESS = 'module_access';
    const FORM_TYPE_BOTH_SERVICE = 'both_service_form';

    // Approval status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_IN_REVIEW = 'in_review';

    // Approval stages
    const STAGE_HOD = 'hod';
    const STAGE_DIVISIONAL_DIRECTOR = 'divisional_director';
    const STAGE_DICT = 'dict';
    const STAGE_HOD_IT = 'hod_it';
    const STAGE_ICT_OFFICER = 'ict_officer';
    const STAGE_COMPLETED = 'completed';

    // Available services for both-service-form
    const AVAILABLE_SERVICES = [
        'wellsoft' => 'Wellsoft',
        'jeeva' => 'Jeeva Access',
        'internet_access' => 'Internet Access Request',
    ];

    // Role mappings
    const ROLE_MAPPINGS = [
        'Head of Department' => self::STAGE_HOD,
        'HOD' => self::STAGE_HOD,
        'Divisional Director' => self::STAGE_DIVISIONAL_DIRECTOR,
        'DICT' => self::STAGE_DICT,
        'Director of ICT' => self::STAGE_DICT,
        'Head of IT' => self::STAGE_HOD_IT,
        'HOD_IT' => self::STAGE_HOD_IT,
        'ICT Officer' => self::STAGE_ICT_OFFICER,
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function hodUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_user_id');
    }

    public function divisionalDirectorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'divisional_director_user_id');
    }

    public function dictUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dict_user_id');
    }

    public function hodItUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_it_user_id');
    }

    public function ictOfficerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ict_officer_user_id');
    }

    /**
     * Scopes
     */
    public function scopeBothServiceForm(Builder $query): Builder
    {
        return $query->where('form_type', self::FORM_TYPE_BOTH_SERVICE);
    }

    public function scopeModuleAccess(Builder $query): Builder
    {
        return $query->where('form_type', self::FORM_TYPE_MODULE_ACCESS);
    }

    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->where('overall_status', self::STATUS_PENDING);
    }

    public function scopeForApprovalStage(Builder $query, string $stage): Builder
    {
        return $query->where('current_approval_stage', $stage);
    }

    public function scopeForRole(Builder $query, string $roleName): Builder
    {
        $stage = self::ROLE_MAPPINGS[$roleName] ?? null;
        if ($stage) {
            return $query->forApprovalStage($stage);
        }
        return $query;
    }

    /**
     * Helper methods
     */
    public function isBothServiceForm(): bool
    {
        return $this->form_type === self::FORM_TYPE_BOTH_SERVICE;
    }

    public function isModuleAccess(): bool
    {
        return $this->form_type === self::FORM_TYPE_MODULE_ACCESS;
    }

    public function canUserApprove(User $user): bool
    {
        $userRole = $user->role?->name;
        if (!$userRole) {
            return false;
        }

        $requiredStage = self::ROLE_MAPPINGS[$userRole] ?? null;
        return $requiredStage && $this->current_approval_stage === $requiredStage;
    }

    public function getApprovalStatusForRole(string $roleName): string
    {
        $stage = self::ROLE_MAPPINGS[$roleName] ?? null;
        if (!$stage) {
            return self::STATUS_PENDING;
        }

        return match ($stage) {
            self::STAGE_HOD => $this->hod_approval_status,
            self::STAGE_DIVISIONAL_DIRECTOR => $this->divisional_director_approval_status,
            self::STAGE_DICT => $this->dict_approval_status,
            self::STAGE_HOD_IT => $this->hod_it_approval_status,
            self::STAGE_ICT_OFFICER => $this->ict_officer_approval_status,
            default => self::STATUS_PENDING,
        };
    }

    public function getNextApprovalStage(): ?string
    {
        return match ($this->current_approval_stage) {
            self::STAGE_HOD => self::STAGE_DIVISIONAL_DIRECTOR,
            self::STAGE_DIVISIONAL_DIRECTOR => self::STAGE_DICT,
            self::STAGE_DICT => self::STAGE_HOD_IT,
            self::STAGE_HOD_IT => self::STAGE_ICT_OFFICER,
            self::STAGE_ICT_OFFICER => self::STAGE_COMPLETED,
            default => null,
        };
    }

    public function moveToNextStage(): void
    {
        $nextStage = $this->getNextApprovalStage();
        if ($nextStage) {
            $this->current_approval_stage = $nextStage;
            if ($nextStage === self::STAGE_COMPLETED) {
                $this->overall_status = self::STATUS_APPROVED;
            } else {
                $this->overall_status = self::STATUS_IN_REVIEW;
            }
        }
    }

    public function rejectRequest(): void
    {
        $this->overall_status = self::STATUS_REJECTED;
    }

    public function getServicesRequestedNames(): array
    {
        if (!$this->services_requested || !is_array($this->services_requested)) {
            return [];
        }

        return array_map(fn($service) => self::AVAILABLE_SERVICES[$service] ?? $service, $this->services_requested);
    }

    public function hasService(string $service): bool
    {
        return in_array($service, $this->services_requested ?? []);
    }

    /**
     * Auto-populate personal information from user data
     */
    public function populatePersonalInfo(): void
    {
        if ($this->user) {
            $this->pf_number = $this->user->pf_number;
            $this->staff_name = $this->user->name;
            $this->phone_number = $this->user->phone;
            
            // Try to get department from user's latest access request or default
            if (!$this->department_id) {
                $latestAccess = UserAccess::where('user_id', $this->user_id)
                    ->latest()
                    ->first();
                if ($latestAccess && $latestAccess->department_id) {
                    $this->department_id = $latestAccess->department_id;
                }
            }
        }
    }
}