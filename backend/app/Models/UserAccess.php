<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserAccess extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'user_access';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'pf_number',
        'staff_name',
        'phone_number',
        'department_id',
        'signature_path',
        'request_type',
        'internet_purposes',
        'module_requested_for',
        'wellsoft_modules_selected',
        'jeeva_modules_selected',
        'access_type',
        'temporary_until',
        'status',
        'divisional_status',
        'hod_status',
        'ict_director_status',
        'head_it_status',
        'ict_officer_status',
        'hod_comments',
        'hod_resubmission_notes',
        'resubmitted_at',
        'resubmitted_by',
        'hod_name',
        'hod_signature_path',
        'hod_approved_at',
        'hod_approved_by',
        'hod_approved_by_name',
        'divisional_director_name',
        'divisional_director_signature_path',
        'divisional_director_comments',
        'divisional_approved_at',
        'ict_director_name',
        'ict_director_signature_path',
        'ict_director_comments',
        'ict_director_rejection_reasons',
        'ict_director_approved_at',
        'head_it_name',
        'head_it_signature_path',
        'head_it_comments',
        'head_it_approved_at',
        'ict_officer_name',
        'ict_officer_signature_path',
        'ict_officer_comments',
        'ict_officer_implemented_at',
        'implementation_comments',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'request_type' => 'array',
        'wellsoft_modules_selected' => 'array',
        'jeeva_modules_selected' => 'array',
        'internet_purposes' => 'array',
        'temporary_until' => 'date',
        'hod_approved_at' => 'datetime',
        'resubmitted_at' => 'datetime',
        'divisional_approved_at' => 'datetime',
        'ict_director_approved_at' => 'datetime',
        'head_it_approved_at' => 'datetime',
        'ict_officer_implemented_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Removed custom accessors and mutators since we're using Laravel's built-in array casting

    /**
     * Request type constants
     */
    const REQUEST_TYPES = [
        'jeeva_access' => 'Jeeva Access',
        'wellsoft' => 'Wellsoft',
        'internet_access_request' => 'Internet Access Request',
    ];

    /**
     * Status constants
     */
    const STATUSES = [
        'pending' => 'Pending',
        'pending_hod' => 'Pending HOD Approval',
        'hod_approved' => 'HOD Approved',
        'hod_rejected' => 'HOD Rejected',
        'pending_divisional' => 'Pending Divisional Director',
        'divisional_approved' => 'Divisional Director Approved',
        'divisional_rejected' => 'Divisional Director Rejected',
        'pending_ict_director' => 'Pending ICT Director',
        'ict_director_approved' => 'ICT Director Approved',
        'ict_director_rejected' => 'ICT Director Rejected',
        'pending_head_it' => 'Pending Head IT',
        'head_it_approved' => 'Head IT Approved',
        'head_it_rejected' => 'Head IT Rejected',
        'pending_ict_officer' => 'Pending ICT Officer',
        'implemented' => 'Implemented',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'in_review' => 'In Review',
        'cancelled' => 'Cancelled',
    ];

    /**
     * Get the user that owns the access request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that the request belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the selected Wellsoft modules for this request.
     */
    public function selectedWellsoftModules(): BelongsToMany
    {
        return $this->belongsToMany(WellsoftModule::class, 'wellsoft_modules_selected', 'user_access_id', 'module_id')
                    ->withTimestamps();
    }

    /**
     * Get the selected Jeeva modules for this request.
     */
    public function selectedJeevaModules(): BelongsToMany
    {
        return $this->belongsToMany(JeevaModule::class, 'jeeva_modules_selected', 'user_access_id', 'module_id')
                    ->withTimestamps();
    }

    /**
     * Check if the access is permanent.
     */
    public function isPermanent(): bool
    {
        return $this->access_type === 'permanent';
    }

    /**
     * Check if the access is temporary.
     */
    public function isTemporary(): bool
    {
        return $this->access_type === 'temporary';
    }

    /**
     * Check if the temporary access has expired.
     */
    public function isExpired(): bool
    {
        if (!$this->isTemporary() || !$this->temporary_until) {
            return false;
        }
        
        return now()->isAfter($this->temporary_until);
    }

    /**
     * Check if HOD has approved this request.
     */
    public function isHodApproved(): bool
    {
        return !empty($this->hod_name) && !empty($this->hod_approved_at);
    }

    /**
     * Check if Divisional Director has approved this request.
     */
    public function isDivisionalDirectorApproved(): bool
    {
        return !empty($this->divisional_director_name) && !empty($this->divisional_approved_at);
    }

    /**
     * Check if ICT Director has approved this request.
     */
    public function isIctDirectorApproved(): bool
    {
        return !empty($this->ict_director_name) && !empty($this->ict_director_approved_at);
    }

    // ========================================
    // STATUS CONSTANTS FOR NEW COLUMNS
    // ========================================
    
    const HOD_STATUSES = ['pending', 'approved', 'rejected'];
    const DIVISIONAL_STATUSES = ['pending', 'approved', 'rejected'];
    const ICT_DIRECTOR_STATUSES = ['pending', 'approved', 'rejected'];
    const HEAD_IT_STATUSES = ['pending', 'approved', 'rejected'];
    const ICT_OFFICER_STATUSES = ['pending', 'implemented', 'rejected'];

    /**
     * Get the approval progress percentage (using new status columns).
     */
    public function getApprovalProgress(): int
    {
        $totalSteps = 3; // HOD, Divisional Director, ICT Director
        $completedSteps = 0;
        
        // Use new status columns if available, fallback to old logic
        if ($this->hod_status === 'approved' || $this->isHodApproved()) $completedSteps++;
        if ($this->divisional_status === 'approved' || $this->isDivisionalDirectorApproved()) $completedSteps++;
        if ($this->ict_director_status === 'approved' || $this->isIctDirectorApproved()) $completedSteps++;
        
        return (int) (($completedSteps / $totalSteps) * 100);
    }

    /**
     * Get the next approval step.
     */
    public function getNextApprovalStep(): ?string
    {
        if (!$this->isHodApproved()) {
            return 'HOD/BM Approval';
        }
        
        if (!$this->isDivisionalDirectorApproved()) {
            return 'Divisional Director Approval';
        }
        
        if (!$this->isIctDirectorApproved()) {
            return 'ICT Director Approval';
        }
        
        if (!$this->isHeadItApproved()) {
            return 'Head of IT Approval';
        }
        
        if (!$this->isIctOfficerImplemented()) {
            return 'ICT Officer Implementation';
        }
        
        return null; // All steps completed
    }

    /**
     * Check if Head of IT has approved this request.
     */
    public function isHeadItApproved(): bool
    {
        return !empty($this->head_it_name) && !empty($this->head_it_approved_at);
    }

    /**
     * Check if ICT Officer has implemented this request.
     */
    public function isIctOfficerImplemented(): bool
    {
        return !empty($this->ict_officer_name) && !empty($this->ict_officer_implemented_at);
    }

    // ========================================
    // NEW APPROVAL STATUS COLUMN HELPERS
    // ========================================

    /**
     * Get the approval status for HOD
     */
    public function getHodApprovalStatus(): string
    {
        return $this->hod_status ?? 'pending';
    }

    /**
     * Check if HOD has approved using new status column
     */
    public function isHodApprovedByStatus(): bool
    {
        return $this->hod_status === 'approved';
    }

    /**
     * Get the approval status for Divisional Director
     */
    public function getDivisionalApprovalStatus(): string
    {
        return $this->divisional_status ?? 'pending';
    }

    /**
     * Check if Divisional Director has approved using new status column
     */
    public function isDivisionalApprovedByStatus(): bool
    {
        return $this->divisional_status === 'approved';
    }

    /**
     * Get the approval status for ICT Director
     */
    public function getIctDirectorApprovalStatus(): string
    {
        return $this->ict_director_status ?? 'pending';
    }

    /**
     * Check if ICT Director has approved using new status column
     */
    public function isIctDirectorApprovedByStatus(): bool
    {
        return $this->ict_director_status === 'approved';
    }

    /**
     * Get the approval status for Head IT
     */
    public function getHeadItApprovalStatus(): string
    {
        return $this->head_it_status ?? 'pending';
    }

    /**
     * Check if Head IT has approved using new status column
     */
    public function isHeadItApprovedByStatus(): bool
    {
        return $this->head_it_status === 'approved';
    }

    /**
     * Get the implementation status for ICT Officer
     */
    public function getIctOfficerImplementationStatus(): string
    {
        return $this->ict_officer_status ?? 'pending';
    }

    /**
     * Check if ICT Officer has implemented using new status column
     */
    public function isIctOfficerImplementedByStatus(): bool
    {
        return $this->ict_officer_status === 'implemented';
    }

    /**
     * Get the calculated overall status from new status columns
     */
    public function getCalculatedOverallStatus(): string
    {
        $migrationService = new \App\Services\StatusMigrationService();
        
        $statusColumns = [
            'hod_status' => $this->hod_status,
            'divisional_status' => $this->divisional_status,
            'ict_director_status' => $this->ict_director_status,
            'head_it_status' => $this->head_it_status,
            'ict_officer_status' => $this->ict_officer_status
        ];
        
        return $migrationService->calculateOverallStatus($statusColumns);
    }

    /**
     * Get the next pending approval stage using new status columns
     */
    public function getNextPendingStageFromColumns(): ?string
    {
        $migrationService = new \App\Services\StatusMigrationService();
        
        $statusColumns = [
            'hod_status' => $this->hod_status,
            'divisional_status' => $this->divisional_status,
            'ict_director_status' => $this->ict_director_status,
            'head_it_status' => $this->head_it_status,
            'ict_officer_status' => $this->ict_officer_status
        ];
        
        return $migrationService->getNextPendingStage($statusColumns);
    }

    /**
     * Check if workflow is complete using new status columns
     */
    public function isWorkflowCompleteByColumns(): bool
    {
        $migrationService = new \App\Services\StatusMigrationService();
        
        $statusColumns = [
            'hod_status' => $this->hod_status,
            'divisional_status' => $this->divisional_status,
            'ict_director_status' => $this->ict_director_status,
            'head_it_status' => $this->head_it_status,
            'ict_officer_status' => $this->ict_officer_status
        ];
        
        return $migrationService->isWorkflowComplete($statusColumns);
    }

    /**
     * Check if workflow has rejections using new status columns
     */
    public function hasRejectionsInColumns(): bool
    {
        $migrationService = new \App\Services\StatusMigrationService();
        
        $statusColumns = [
            'hod_status' => $this->hod_status,
            'divisional_status' => $this->divisional_status,
            'ict_director_status' => $this->ict_director_status,
            'head_it_status' => $this->head_it_status,
            'ict_officer_status' => $this->ict_officer_status
        ];
        
        return $migrationService->hasRejections($statusColumns);
    }

    /**
     * Get workflow progress percentage using new status columns
     */
    public function getWorkflowProgressFromColumns(): int
    {
        $migrationService = new \App\Services\StatusMigrationService();
        
        $statusColumns = [
            'hod_status' => $this->hod_status,
            'divisional_status' => $this->divisional_status,
            'ict_director_status' => $this->ict_director_status,
            'head_it_status' => $this->head_it_status,
            'ict_officer_status' => $this->ict_officer_status
        ];
        
        return $migrationService->getWorkflowProgress($statusColumns);
    }

    /**
     * Check if the implementation workflow is complete.
     */
    public function isImplementationComplete(): bool
    {
        return $this->isHeadItApproved() && $this->isIctOfficerImplemented();
    }

    /**
     * Get the implementation progress percentage.
     */
    public function getImplementationProgress(): int
    {
        $totalSteps = 2; // Head of IT, ICT Officer
        $completedSteps = 0;
        
        if ($this->isHeadItApproved()) $completedSteps++;
        if ($this->isIctOfficerImplemented()) $completedSteps++;
        
        return (int) (($completedSteps / $totalSteps) * 100);
    }

    /**
     * Get the complete workflow progress percentage (approval + implementation).
     */
    public function getCompleteWorkflowProgress(): int
    {
        $totalSteps = 5; // HOD, Divisional Director, ICT Director, Head of IT, ICT Officer
        $completedSteps = 0;
        
        if ($this->isHodApproved()) $completedSteps++;
        if ($this->isDivisionalDirectorApproved()) $completedSteps++;
        if ($this->isIctDirectorApproved()) $completedSteps++;
        if ($this->isHeadItApproved()) $completedSteps++;
        if ($this->isIctOfficerImplemented()) $completedSteps++;
        
        return (int) (($completedSteps / $totalSteps) * 100);
    }

    /**
     * Get the current workflow stage.
     */
    public function getCurrentWorkflowStage(): string
    {
        if (!$this->isHodApproved()) {
            return 'Pending HOD Approval';
        }
        
        if (!$this->isDivisionalDirectorApproved()) {
            return 'Pending Divisional Director Approval';
        }
        
        if (!$this->isIctDirectorApproved()) {
            return 'Pending ICT Director Approval';
        }
        
        if (!$this->isHeadItApproved()) {
            return 'Pending Head of IT Approval';
        }
        
        if (!$this->isIctOfficerImplemented()) {
            return 'Pending ICT Officer Implementation';
        }
        
        return 'Implementation Complete';
    }

    /**
     * Scope a query to only include requests of a given type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->whereJsonContains('request_type', $type);
    }

    /**
     * Scope a query to only include requests with a given status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the formatted request type name.
     */
    public function getRequestTypeNameAttribute(): string
    {
        if (is_array($this->request_type)) {
            return implode(', ', array_map(fn($type) => self::REQUEST_TYPES[$type] ?? $type, $this->request_type));
        }
        return self::REQUEST_TYPES[$this->request_type] ?? $this->request_type;
    }

    /**
     * Get the formatted status name.
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the next approval needed in the workflow
     */
    public function getNextApprovalNeeded(): string
    {
        // If no HOD approval yet
        if (empty($this->hod_approved_at)) {
            return 'HOD/BM Approval';
        }
        
        // If no divisional director approval yet
        if (empty($this->divisional_approved_at)) {
            return 'Divisional Director Approval';
        }
        
        // If no ICT director approval yet
        if (empty($this->ict_director_approved_at)) {
            return 'ICT Director Approval';
        }
        
        // If no Head IT approval yet
        if (empty($this->head_it_approved_at)) {
            return 'Head IT Approval';
        }
        
        // If no ICT Officer implementation yet
        if (empty($this->ict_officer_implemented_at)) {
            return 'ICT Officer Implementation';
        }
        
        return 'Complete';
    }

    /**
     * Get the current approval stage
     */
    public function getCurrentApprovalStage(): array
    {
        $stages = [
            'hod' => [
                'name' => 'HOD/BM',
                'completed' => !empty($this->hod_approved_at),
                'has_signature' => !empty($this->hod_signature_path),
                'approved_by' => $this->hod_name,
                'approved_at' => $this->hod_approved_at
            ],
            'divisional' => [
                'name' => 'Divisional Director',
                'completed' => !empty($this->divisional_approved_at),
                'has_signature' => !empty($this->divisional_director_signature_path),
                'approved_by' => $this->divisional_director_name,
                'approved_at' => $this->divisional_approved_at
            ],
            'ict_director' => [
                'name' => 'ICT Director',
                'completed' => !empty($this->ict_director_approved_at),
                'has_signature' => !empty($this->ict_director_signature_path),
                'approved_by' => $this->ict_director_name,
                'approved_at' => $this->ict_director_approved_at
            ],
            'head_it' => [
                'name' => 'Head IT',
                'completed' => !empty($this->head_it_approved_at),
                'has_signature' => !empty($this->head_it_signature_path),
                'approved_by' => $this->head_it_name,
                'approved_at' => $this->head_it_approved_at
            ],
            'ict_officer' => [
                'name' => 'ICT Officer',
                'completed' => !empty($this->ict_officer_implemented_at),
                'has_signature' => !empty($this->ict_officer_signature_path),
                'implemented_by' => $this->ict_officer_name,
                'implemented_at' => $this->ict_officer_implemented_at
            ]
        ];
        
        return $stages;
    }

    /**
     * Check if the request is in review.
     */
    public function isInReview(): bool
    {
        return $this->status === 'in_review';
    }

    /**
     * Check if the request contains a specific request type.
     */
    public function hasRequestType(string $type): bool
    {
        if (is_array($this->request_type)) {
            return in_array($type, $this->request_type);
        }
        return $this->request_type === $type;
    }

    /**
     * Get all request types as an array.
     */
    public function getRequestTypesArray(): array
    {
        if (is_array($this->request_type)) {
            return $this->request_type;
        }
        return [$this->request_type];
    }

    /**
     * Access type constants
     */
    const ACCESS_TYPES = [
        'permanent' => 'Permanent (until retirement)',
        'temporary' => 'Temporary'
    ];

    /**
     * Get next approval stage based on current status
     */
    public function getNextApprovalStage(): ?string
    {
        return match($this->status) {
            'pending', 'pending_hod' => 'hod',
            'hod_approved', 'pending_divisional' => 'divisional_director',
            'divisional_approved', 'pending_ict_director' => 'ict_director',
            'ict_director_approved', 'pending_head_it' => 'head_it',
            'head_it_approved', 'pending_ict_officer' => 'ict_officer',
            default => null
        };
    }

    /**
     * Check if request is at a specific approval stage
     */
    public function isAtStage(string $stage): bool
    {
        return match($stage) {
            'hod' => in_array($this->status, ['pending', 'pending_hod']),
            'divisional_director' => in_array($this->status, ['hod_approved', 'pending_divisional']),
            'ict_director' => in_array($this->status, ['divisional_approved', 'pending_ict_director']),
            'head_it' => in_array($this->status, ['ict_director_approved', 'pending_head_it']),
            'ict_officer' => in_array($this->status, ['head_it_approved', 'pending_ict_officer']),
            default => false
        };
    }

    /**
     * Check if request has specific modules
     */
    public function hasWellsoftModules(): bool
    {
        return !empty($this->wellsoft_modules_selected);
    }

    public function hasJeevaModules(): bool
    {
        return !empty($this->jeeva_modules_selected);
    }

    public function hasInternetPurposes(): bool
    {
        return !empty($this->internet_purposes);
    }

    /**
     * Get formatted access type
     */
    public function getAccessTypeNameAttribute(): string
    {
        return self::ACCESS_TYPES[$this->access_type] ?? $this->access_type;
    }

    /**
     * Check if the request can be updated/resubmitted.
     * Only pending or rejected requests can be updated.
     */
    public function canBeUpdated(): bool
    {
        $updatableStatuses = [
            'pending',
            'hod_rejected',
            'divisional_rejected',
            'ict_director_rejected',
            'head_it_rejected',
            'rejected'
        ];
        
        return in_array($this->status, $updatableStatuses);
    }

    /**
     * Get all modules as a combined array
     */
    public function getAllModulesAttribute(): array
    {
        $modules = [];
        
        if ($this->wellsoft_modules_selected) {
            foreach ($this->wellsoft_modules_selected as $module) {
                $modules[] = ['type' => 'Wellsoft', 'name' => $module];
            }
        }
        
        if ($this->jeeva_modules_selected) {
            foreach ($this->jeeva_modules_selected as $module) {
                $modules[] = ['type' => 'Jeeva', 'name' => $module];
            }
        }
        
        return $modules;
    }
    
    /**
     * Get selected Wellsoft modules
     */
    public function getSelectedWellsoftModules(): array
    {
        return $this->wellsoft_modules_selected ?? [];
    }
    
    /**
     * Get selected Jeeva modules
     */
    public function getSelectedJeevaModules(): array
    {
        return $this->jeeva_modules_selected ?? [];
    }
    
    /**
     * Check if request has selected Wellsoft modules
     */
    public function hasSelectedWellsoftModules(): bool
    {
        return !empty($this->wellsoft_modules_selected);
    }
    
    /**
     * Check if request has selected Jeeva modules
     */
    public function hasSelectedJeevaModules(): bool
    {
        return !empty($this->jeeva_modules_selected);
    }
    
    /**
     * Get module requested for (use/revoke)
     */
    public function getModuleRequestedForAttribute(): string
    {
        return $this->attributes['module_requested_for'] ?? 'use';
    }
    
    /**
     * Check if modules are requested for use
     */
    public function isModuleRequestForUse(): bool
    {
        return $this->module_requested_for === 'use';
    }
    
    /**
     * Check if modules are requested for revocation
     */
    public function isModuleRequestForRevoke(): bool
    {
        return $this->module_requested_for === 'revoke';
    }
}
