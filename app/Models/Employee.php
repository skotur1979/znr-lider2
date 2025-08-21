<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    // Dozvoli mass assignment za sva relevantna polja
    protected $fillable = [
        'user_id',
        'name',
        'job_title',
        'education',
        'place_of_birth',
        'name_of_parents',
        'address',
        'gender',
        'OIB',
        'phone',
        'email',
        'workplace',
        'organization_unit',
        'contract_type',
        'employeed_at',
        'contract_ended_at',
        'medical_examination_valid_from',
        'medical_examination_valid_until',
        'article',
        'remark',
        'occupational_safety_valid_from',
        'fire_protection_valid_from',
        'fire_protection_statement_at',
        'evacuation_valid_from',
        'first_aid_valid_from',
        'first_aid_valid_until',
        'toxicology_valid_from',
        'toxicology_valid_until',
        'handling_flammable_materials_valid_from',
        'handling_flammable_materials_valid_until',
        'employers_authorization_valid_from',
        'employers_authorization_valid_until',
        'pdf',
    ];

    // Cast datuma za automatsku pretvorbu u Carbon objekte
    protected $casts = [
        'employeed_at' => 'date',
        'medical_examination_valid_from' => 'date',
        'medical_examination_valid_until' => 'date',
        'occupational_safety_valid_from' => 'date',
        'fire_protection_valid_from' => 'date',
        'fire_protection_statement_at' => 'date',
        'evacuation_valid_from' => 'date',
        'first_aid_valid_from' => 'date',
        'toxicology_valid_from' => 'date',
        'toxicology_valid_until' => 'date',
        'handling_flammable_materials_valid_from' => 'date',
        'handling_flammable_materials_valid_until' => 'date',
        'employers_authorization_valid_from' => 'date',
        'employers_authorization_valid_until' => 'date',
        'pdf' => 'array',
    ];

    // Pravila za validaciju PDF-a
    public static function rules()
    {
        return [
            'pdf' => 'nullable|file|mimes:pdf|max:20240',
        ];
    }

    // Relacija prema korisniku koji je unio zapis
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function certificates()
{
    return $this->hasMany(\App\Models\EmployeeCertificate::class);
}
public function getCertificatesFilteredAttribute()
{
    $certificates = $this->certificates;

    $hasExpired = request()->has('force_certificates_expired');
$hasExpiring = request()->has('force_certificates_expiring');

    // Ako su oba aktivna → prikaži istekle ILI uskoro ističuće
    if ($hasExpired && $hasExpiring) {
        return $certificates->filter(function ($certificate) {
            $validUntil = $certificate->valid_until ? \Carbon\Carbon::parse($certificate->valid_until) : null;
            return $validUntil && (
                $validUntil->lt(now()) ||
                $validUntil->isBetween(now(), now()->addDays(30))
            );
        })->map(function ($c) {
            $validUntil = $c->valid_until ? \Carbon\Carbon::parse($c->valid_until) : null;
            $c->highlight = 'white';

            if ($validUntil && $validUntil->lt(now())) {
                $c->highlight = 'red';
            } elseif ($validUntil && $validUntil->diffInDays(now()) <= 30) {
                $c->highlight = 'gold';
            }

            return $c;
        });
    }

    // Samo istekle
    if ($hasExpired) {
        return $certificates->filter(function ($certificate) {
            return $certificate->valid_until && \Carbon\Carbon::parse($certificate->valid_until)->lt(now());
        })->map(function ($c) {
            $c->highlight = 'red';
            return $c;
        });
    }

    // Samo uskoro ističu
    if ($hasExpiring) {
        return $certificates->filter(function ($certificate) {
            $validUntil = \Carbon\Carbon::parse($certificate->valid_until);
            return $validUntil->gte(now()) && $validUntil->lte(now()->addDays(30));
        })->map(function ($c) {
            $c->highlight = 'gold';
            return $c;
        });
    }

    // Ako nijedan filter nije aktivan → prikaži sve
    return $certificates->map(function ($certificate) {
        $validUntil = $certificate->valid_until ? \Carbon\Carbon::parse($certificate->valid_until) : null;
        $certificate->highlight = 'white';

        if ($validUntil && $validUntil->lt(now())) {
            $certificate->highlight = 'red';
        } elseif ($validUntil && $validUntil->diffInDays(now()) <= 30) {
            $certificate->highlight = 'gold';
        }

        return $certificate;
    });
}
public function medicalReferrals()
{
    return $this->hasMany(\App\Models\MedicalReferral::class);
}


}