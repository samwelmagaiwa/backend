<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JeevaAccessRequest extends Model
{
    protected $fillable = [
        'pf_number',
        'staff_name',
        'signature',
        'module_action',
        'modules',
        'access_rights',
        'access_duration',
        'temporary_until',
        'hodbm_name',
        'hodbm_signature',
        'hodbm_date',
        'divdir_name',
        'divdir_signature',
        'divdir_date',
        'ictdir_name',
        'ictdir_signature_date',
        'ictdir_comments',
        'headit_name',
        'headit_signature_date',
        'ict_officer_name',
        'ict_officer_signature_date',
    ];
}
