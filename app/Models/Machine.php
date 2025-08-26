<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'manufacturer', // Proizvođač
        'factory_number', // Tvornički broj
        'inventory_number', // Inventarni broj
        'location',
        'examination_valid_from',
        'examination_valid_until',
        'examined_by', // Ispitao
        'report_number', // Broj izvještaja
        'remark',
        'pdf',
        // dodaj i ostala polja koja koristiš u formi
    ];

    protected $casts = [
        'examination_valid_from' => 'date',
        'examination_valid_until' => 'date',
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

