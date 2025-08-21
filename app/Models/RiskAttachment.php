<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiskAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'risk_assessment_id',
        'naziv',
        'file_path',
    ];

    public function riskAssessment()
    {
        return $this->belongsTo(RiskAssessment::class);
    }
}

