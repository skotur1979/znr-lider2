<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiskAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tvrtka', 'oib_tvrtke', 'adresa_tvrtke',
        'broj_procjene', 'datum_izrade', 'vrsta_procjene',
    ];

    public function participants()
    {
        return $this->hasMany(RiskParticipant::class);
    }

    public function revisions()
    {
        return $this->hasMany(RiskRevision::class);
    }

    public function attachments()
    {
        return $this->hasMany(RiskAttachment::class);
    }
}
