<?php

namespace App\Models;

/**
 * UserCombinedAccessRequest extends UserAccess
 * This model represents the same access requests but with additional
 * functionality specific to Head of IT workflow management.
 */
class UserCombinedAccessRequest extends UserAccess
{
    // Additional fillable fields for Head of IT workflow
    protected $fillable = [
        // Base fields from UserAccess
        'staff_name',
        'pf_number', 
        'phone_number',
        'department',
        'department_name',
        'request_type',
        'internet_purposes',
        'status',
        'created_at',
        'updated_at',
        'hod_approved_at',
        'divisional_approved_at', 
        'ict_director_approved_at',
        'user_id',
        'position',
        
        // Additional fields for Head of IT workflow
        'request_id',
        'head_of_it_approved_at',
        'head_of_it_approved_by',
        'head_of_it_rejected_at', 
        'head_of_it_rejected_by',
        'head_of_it_signature_path',
        'head_of_it_rejection_reason',
        'head_of_it_comments',
        'assigned_ict_officer_id',
        'task_assigned_at'
    ];

    // Additional casts for Head of IT workflow
    protected $casts = [
        // Base casts from UserAccess
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'hod_approved_at' => 'datetime',
        'divisional_approved_at' => 'datetime',
        'ict_director_approved_at' => 'datetime',
        'request_type' => 'array',
        'internet_purposes' => 'array',
        
        // Additional casts for Head of IT workflow
        'head_of_it_approved_at' => 'datetime',
        'head_of_it_rejected_at' => 'datetime',
        'task_assigned_at' => 'datetime'
    ];

    /**
     * Get the request ID attribute or generate one
     */
    public function getRequestIdAttribute($value)
    {
        return $value ?? 'REQ-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the department name, fallback to relationship
     */
    public function getDepartmentNameAttribute($value)
    {
        return $value ?? $this->department->name ?? 'Unknown Department';
    }

    /**
     * Get request types as array (accessor for request_types field)
     * This provides compatibility while using request_type as the actual field
     */
    public function getRequestTypesAttribute()
    {
        return $this->getRequestTypesArray();
    }

    /**
     * Get the assigned ICT Officer
     */
    public function assignedIctOfficer()
    {
        return $this->belongsTo(User::class, 'assigned_ict_officer_id');
    }

    /**
     * Get the Head of IT who approved this request
     */
    public function headOfItApprovedBy()
    {
        return $this->belongsTo(User::class, 'head_of_it_approved_by');
    }

    /**
     * Get the Head of IT who rejected this request
     */
    public function headOfItRejectedBy()
    {
        return $this->belongsTo(User::class, 'head_of_it_rejected_by');
    }

    /**
     * Get task assignments for this request
     */
    public function taskAssignments()
    {
        return $this->hasMany(TaskAssignment::class, 'request_id');
    }

    /**
     * Get the current active task assignment
     */
    public function currentTaskAssignment()
    {
        return $this->hasOne(TaskAssignment::class, 'request_id')
            ->whereIn('status', ['assigned', 'in_progress'])
            ->latest();
    }

    /**
     * Check if Head of IT has approved this request
     */
    public function isHeadOfItApproved(): bool
    {
        return !empty($this->head_of_it_approved_at);
    }

    /**
     * Check if Head of IT has rejected this request
     */
    public function isHeadOfItRejected(): bool
    {
        return !empty($this->head_of_it_rejected_at);
    }

    /**
     * Check if task is assigned to an ICT Officer
     */
    public function isTaskAssigned(): bool
    {
        return !empty($this->assigned_ict_officer_id);
    }

    /**
     * Scope for requests pending Head of IT approval
     */
    public function scopePendingHeadOfItApproval($query)
    {
        return $query->where('status', 'ict_director_approved')
            ->whereNull('head_of_it_approved_at')
            ->whereNull('head_of_it_rejected_at');
    }

    /**
     * Scope for requests approved by Head of IT
     */
    public function scopeHeadOfItApproved($query)
    {
        return $query->where('status', 'head_of_it_approved')
            ->whereNotNull('head_of_it_approved_at');
    }

    /**
     * Scope for requests rejected by Head of IT
     */
    public function scopeHeadOfItRejected($query)
    {
        return $query->where('status', 'head_of_it_rejected')
            ->whereNotNull('head_of_it_rejected_at');
    }

    /**
     * Scope for requests assigned to ICT Officers
     */
    public function scopeAssignedToIct($query)
    {
        return $query->where('status', 'assigned_to_ict')
            ->whereNotNull('assigned_ict_officer_id');
    }

    /**
     * Get the workflow status for Head of IT dashboard
     */
    public function getWorkflowStatusForHeadOfIt(): string
    {
        if ($this->isHeadOfItRejected()) {
            return 'Rejected by Head of IT';
        }
        
        if ($this->isTaskAssigned()) {
            return 'Assigned to ICT Officer';
        }
        
        if ($this->isHeadOfItApproved()) {
            return 'Approved - Pending Assignment';
        }
        
        if ($this->status === 'ict_director_approved') {
            return 'Pending Head of IT Approval';
        }
        
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get next action required for Head of IT
     */
    public function getNextActionForHeadOfIt(): ?string
    {
        if ($this->status === 'ict_director_approved' && !$this->isHeadOfItApproved() && !$this->isHeadOfItRejected()) {
            return 'Review and Approve/Reject';
        }
        
        if ($this->isHeadOfItApproved() && !$this->isTaskAssigned()) {
            return 'Assign to ICT Officer';
        }
        
        return null;
    }

    /**
     * Get summary for Head of IT dashboard
     */
    public function getSummaryForHeadOfIt(): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'staff_name' => $this->staff_name,
            'department' => $this->department_name,
            'request_types' => $this->request_types,
            'status' => $this->status,
            'workflow_status' => $this->getWorkflowStatusForHeadOfIt(),
            'next_action' => $this->getNextActionForHeadOfIt(),
            'created_at' => $this->created_at,
            'ict_director_approved_at' => $this->ict_director_approved_at,
            'head_of_it_approved_at' => $this->head_of_it_approved_at,
            'assigned_ict_officer' => $this->assignedIctOfficer?->name,
            'task_assigned_at' => $this->task_assigned_at
        ];
    }
}
