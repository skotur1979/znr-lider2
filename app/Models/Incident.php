<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Incident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',                // ⬅️ VAŽNO
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
public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted(): void
{
    static::creating(function ($model) {
        if (blank($model->user_id) && Auth::check()) {
            $model->user_id = Auth::id();
        }
    });
}
}