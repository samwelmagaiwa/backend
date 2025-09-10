<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'assessment_type', // issuing | receiving
        'physical_condition',
        'functionality',
        'accessories_complete',
        'has_damage',
        'damage_description',
        'notes',
        'assessed_by',
        'assessed_at',
    ];
}
