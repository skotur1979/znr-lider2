<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PersonalProtectiveEquipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_id',
        'equipment_name',
        'size',
        'duration_months',
        'issue_date',
        'end_date',
        'signature',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'end_date' => 'date',
    ];

    public function log()
{
    return $this->belongsTo(PersonalProtectiveEquipmentLog::class, 'personal_protective_equipment_log_id');
}


    // ✅ Accessor za prikaz izračunatog datuma isteka
    public function getCalculatedEndDateAttribute(): ?string
    {
        if ($this->issue_date && $this->duration_months) {
            return $this->issue_date->copy()->addMonths($this->duration_months)->format('Y-m-d');
        }

        return null;
    }

    // ✅ Mutator koji automatski postavlja end_date ako postoji issue_date + duration_months
    protected static function booted(): void
    {
        static::saving(function (self $item) {
            if ($item->issue_date && $item->duration_months) {
                $item->end_date = $item->issue_date->copy()->addMonths($item->duration_months);
            }
        });
    }
}
