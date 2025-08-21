<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Observation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'incident_date',
        'observation_type',
        'location',
        'item',
        'potential_incident_type',
        'action',
        'responsible',
        'target_date',
        'status',
        'comments',
        'picture_path',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'target_date' => 'date',
    ];
}

