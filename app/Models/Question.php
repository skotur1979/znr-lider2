<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','test_id','tekst','visestruki_odgovori','slika_path'];

    public function user()   { return $this->belongsTo(User::class); }
    public function test()   { return $this->belongsTo(Test::class); }
    public function answers(){ return $this->hasMany(Answer::class); }
}
