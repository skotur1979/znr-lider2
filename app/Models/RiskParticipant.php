<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiskParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'risk_assessment_id',
        'ime_prezime',
        'uloga',
        'napomena',
    ];

    public function riskAssessment()
    {
        return $this->belongsTo(RiskAssessment::class);
    }
}
