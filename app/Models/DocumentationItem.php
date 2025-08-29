<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationItem extends Model
{
    use HasFactory;
    protected $fillable = [
    'user_id',
    'naziv',
    'tvrtka',
    'datum_izrade',
    'status_napomena',
    'prilozi', // ako koristiš jedno upload polje
    'podaci',  // ako koristiš Repeater koji sprema JSON
];


protected $casts = [
    'prilozi' => 'array',
];
public function user()
    {
        return $this->belongsTo(User::class);
    }
}