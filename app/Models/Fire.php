<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fire extends Model
{
    use HasFactory, SoftDeletes;

    // ✅ Koristi samo $fillable — navedi SVA polja koja se unose preko forme
    protected $fillable = [
    'user_id',
    'place',
    'type',
    'factory_number/year_of_production',
    'serial_label_number',
    'examination_valid_from',
    'examination_valid_until',
    'regular_examination_valid_from',
    'regular_examination_valid_until',
    'service',
    'visible',
    'remark',
    'action',
    'pdf',
    ];

    protected $casts = [
        'examination_valid_from' => 'date',
        'examination_valid_until' => 'date',
        'regular_examination_valid_from' => 'date',
        'pdf' => 'array',
    ];

    public static function rules()
    {
        return [
            'pdf' => 'nullable|file|mimes:pdf|max:20240',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}