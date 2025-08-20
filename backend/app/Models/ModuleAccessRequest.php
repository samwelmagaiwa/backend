<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleAccessRequest extends Model
{
    protected $fillable = [
        'user_id',
        'access_type',
        'temporary_until',
        'modules',
        'comments',
    ];

    protected $casts = [
        'modules' => 'array',
        'temporary_until' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
