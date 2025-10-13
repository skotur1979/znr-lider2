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
        'signature',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'end_date' => 'date',
    ];

    public function log()
    {
        return $this->belongsTo(
            PersonalProtectiveEquipmentLog::class,
            'personal_protective_equipment_log_id'
        );
    }

    public function getCalculatedEndDateAttribute(): ?string
    {
        if ($this->issue_date && $this->duration_months) {
            return $this->issue_date->copy()
                ->addMonths($this->duration_months)
                ->format('Y-m-d');
        }
        return null;
    }

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            if ($item->issue_date && $item->duration_months) {
                $item->end_date = $item->issue_date->copy()->addMonths($item->duration_months);
            }
        });
    }

    /** Base64 -> file na 'public' disku; u DB samo path */
    public function setSignatureAttribute($value): void
    {
        if (blank($value)) {
            $this->attributes['signature'] = null;
            return;
        }

        if (is_string($value) && ! str_starts_with($value, 'data:image')) {
            $this->attributes['signature'] = $value; // već je path
            return;
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $value, $m)) {
            $ext = strtolower($m[1]);
            $ext = $ext === 'jpeg' ? 'jpg' : $ext;

            $raw = substr($value, strpos($value, ',') + 1);
            $bin = base64_decode($raw);

            $dir  = 'signatures';
            $name = 'sig_' . date('Ymd_His') . '_' . Str::random(8) . '.' . $ext;
            $path = $dir . '/' . $name;

            Storage::disk('public')->put($path, $bin);

            $this->attributes['signature'] = $path;
            return;
        }

        $this->attributes['signature'] = null;
    }
}


