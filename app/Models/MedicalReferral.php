<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalReferral extends Model
{
    use HasFactory;

    protected $fillable = [
    'employee_id',
    'name_of_parents',
    'law_reference1',
    'special_conditions',
    'last_exam_reference1',
    'last_exam_reference2',
    'last_exam_reference3',
    'lifting_enabled',
    'lifting_weight',
    'carrying_enabled',
    'carrying_weight',
    'pushing_enabled',
    'pushing_weight',
    'job_characteristics',
    'hazards',
    'chemcial_substances',
    'biological_hazards',
    'employer_name',
    'employer_address',
    'full_name',
    'place_of_birth',
    'oib',
    'job_title',
    'education',
    'employment_date',
    'health_jobs_description',
    'law_reference',
    'special_conditions',
    'exam_type',
    'last_exam_date',
    'last_exam_reference',
    'short_description',
    'tools',
    'job_tasks',
    'referral_number',
    'referral_date',
    'employer_oib',
    'total_years',
    'work_years_in_job',
    'workplace_location',
    'organization',
    'body_position',
    'loads',
    'hazards',
];

    protected $casts = [
        'exam_type' => 'array',
        'workplace_location' => 'array',
        'organization' => 'array',
        'body_position' => 'array',
        'loads' => 'array',
        'hazards' => 'array',
        'job_characteristics' => 'array',
    
    ];

    public function employee()
{
    return $this->belongsTo(\App\Models\Employee::class);
}

public function getDisplayNameAttribute(): string
{
    return $this->employee->name ?? (string) $this->full_name;
}
}