<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location',
        'type_of_incident',
        'permanent_or_temporary',
        'date_occurred',
        'date_of_return',
        'working_days_lost',
        'causes_of_injury',
        'accident_injury_type',
        'injured_body_part',
        'image_path',
        'other',
        'investigation_report',
        'active',
    ];

    protected $casts = [
    'active' => 'boolean',
    'investigation_report' => 'array',
    'pdf' => 'array',
];
}

