<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
    'budget_id',
    'naziv_troska',
    'iznos',
    'dobavljac',
    'mjesec',
    'realizirano',
];
public function budget() {
    return $this->belongsTo(Budget::class);
}
}