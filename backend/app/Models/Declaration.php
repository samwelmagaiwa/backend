<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Declaration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'full_name',
        'pf_number',
        'department',
        'job_title',
        'signature_date',
        'agreement_accepted',
        'signature_info',
        'ip_address',
        'user_agent',
        'submitted_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'signature_date' => 'date',
        'agreement_accepted' => 'boolean',
        'signature_info' => 'array',
        'submitted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the declaration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include declarations with accepted agreements.
     */
    public function scopeAccepted($query)
    {
        return $query->where('agreement_accepted', true);
    }

    /**
     * Scope a query to filter by PF number.
     */
    public function scopeByPfNumber($query, $pfNumber)
    {
        return $query->where('pf_number', $pfNumber);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Get the signature file path if exists.
     */
    public function getSignaturePathAttribute(): ?string
    {
        return $this->signature_info['path'] ?? null;
    }

    /**
     * Get the signature file URL if exists.
     */
    public function getSignatureUrlAttribute(): ?string
    {
        if ($this->signature_path) {
            return asset('storage/' . $this->signature_path);
        }
        return null;
    }

    /**
     * Check if declaration has a signature file.
     */
    public function hasSignature(): bool
    {
        return !empty($this->signature_info['path']);
    }

    /**
     * Get formatted submission date.
     */
    public function getFormattedSubmittedAtAttribute(): string
    {
        return $this->submitted_at->format('M d, Y \a\t H:i');
    }
}