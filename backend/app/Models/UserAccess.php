<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'purpose',
        'request_type',
        'wellsoft_modules',
        'jeeva_modules',
        'internet_purposes',
        'module_requested_for',
        'wellsoft_modules_selected',
        'jeeva_modules_selected',
        'access_type',
        'temporary_until',
        'status',
        'hod_comments',
        'hod_name',
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
        'purpose' => 'array',
        'wellsoft_modules' => 'array',
        'jeeva_modules' => 'array',
        'wellsoft_modules_selected' => 'array',
        'jeeva_modules_selected' => 'array',
        'internet_purposes' => 'array',
        'temporary_until' => 'date',
        'hod_approved_at' => 'datetime',
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
        return !empty($this->wellsoft_modules);
    }

    public function hasJeevaModules(): bool
    {
        return !empty($this->jeeva_modules);
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
     * Check if access is temporary
     */
    public function isTemporary(): bool
    {
        return $this->access_type === 'temporary';
    }

    /**
     * Check if access is permanent
     */
    public function isPermanent(): bool
    {
        return $this->access_type === 'permanent';
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
