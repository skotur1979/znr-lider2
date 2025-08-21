<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiskRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'risk_assessment_id',
        'revizija_broj',
        'datum_izrade',
    ];

    public function riskAssessment()
    {
        return $this->belongsTo(RiskAssessment::class);
    }
}
