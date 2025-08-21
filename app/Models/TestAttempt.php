<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'user_id',
        'ime_prezime',
        'radno_mjesto',
        'datum_rodjenja',
        'bodovi_osvojeni',
        'rezultat',
        'prolaz',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function odgovori()
    {
        return $this->hasMany(AttemptAnswer::class);
    }
}