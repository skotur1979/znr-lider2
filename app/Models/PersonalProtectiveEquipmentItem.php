<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PersonalProtectiveEquipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'personal_protective_equipment_log_id',
        'equipment_name',
        'size',
        'duration_months',
        'issue_date',
        'standard',
        'return_date',
        'end_date',
        'signature', // path do PNG-a ili base64 (mutator će prebaciti u PNG)
    ];

    protected $casts = [
        'issue_date' => 'date',
        'end_date'   => 'date',
        'return_date'=> 'date',
    ];

    public function log()
    {
        return $this->belongsTo(
            PersonalProtectiveEquipmentLog::class,
            'personal_protective_equipment_log_id'
        );
    }

    /** Izračun isteka (za prikaz ako treba) */
    public function getCalculatedEndDateAttribute(): ?string
    {
        if ($this->issue_date && $this->duration_months) {
            return $this->issue_date->copy()
                ->addMonths((int) $this->duration_months)
                ->format('Y-m-d');
        }
        return null;
    }

    /** Mutator: base64 potpis -> PNG na public disku; već postojeći path ostaje netaknut */
    public function setSignatureAttribute($value): void
    {
        // Ako je već path (npr. "signatures/ozo/xx.png"), samo postavi
        if (is_string($value) && ! str_starts_with($value, 'data:image')) {
            $this->attributes['signature'] = $value;
            return;
        }

        // Ako je base64 data URL, spremi kao PNG
        if (is_string($value) && str_starts_with($value, 'data:image')) {
            [$meta, $data] = explode(',', $value, 2);
            $png  = base64_decode($data);
            $name = 'signatures/ozo/' . now()->format('Ymd_His') . '_' . Str::random(8) . '.png';

            Storage::disk('public')->put($name, $png);
            $this->attributes['signature'] = $name;
            return;
        }

        // Inače null
        $this->attributes['signature'] = null;
    }

    /** Prije spremanja izračunaj end_date iz issue_date + duration_months */
    protected static function booted(): void
    {
        static::saving(function (self $item) {
            if ($item->issue_date && $item->duration_months) {
                $issue = $item->issue_date instanceof Carbon
                    ? $item->issue_date
                    : Carbon::parse($item->issue_date);

                $item->end_date = $issue->copy()->addMonths((int) $item->duration_months);
            }
        });
    }
}
