<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Miscellaneous extends Model
{
    use HasFactory, SoftDeletes;

    // Ako koristiš $fillable, nemoj koristiti $guarded
    protected $fillable = [
        'user_id',
        'name',
        'category_id',
        'examiner',
        'report_number',
        'location',
        'examination_valid_from',
        'examination_valid_until',
        'remark',
        'pdf',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}