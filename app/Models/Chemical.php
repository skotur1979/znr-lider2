<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chemical extends Model
{
    use HasFactory, SoftDeletes;
protected $fillable = [
        'product_name',
        'cas_number',
        'ufi_number',
        'hazard_pictograms',
        'h_statements',
        'p_statements',
        'usage_location',
        'annual_quantity',
        'gvi_kgvi', // ➕ Dodano novo polje GVI / KGVI
        'voc', // ➕ Dodano ovdje
        'stl_hzjz',
        'attachments'
    ];

    protected $casts = [
        'hazard_pictograms' => 'array',
        'h_statements' => 'array',
        'p_statements' => 'array',
        'stl_hzjz' => 'date',
        'pdf' => 'array',
        'attachments' => 'array', // Automatski pretvara JSON u PHP niz
    ];
    public static function rules()
    {
        return [
            'attachments' => 'nullable|file|mimes:pdf|max:20240',
            'pdf' => 'nullable|file|mimes:pdf|max:20240',
        ];
    }
}

