<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationItem extends Model
{
    use HasFactory;
    protected $fillable = [
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
}