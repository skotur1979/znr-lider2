<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

protected $fillable = [
    'godina',
    'ukupni_budget',
];

public function expenses()
{
    return $this->hasMany(Expense::class);
}
}
