<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FirstAidKit extends Model
{
    use HasFactory;

    protected $fillable = ['location', 'inspected_at', 'note'];

    public function items()
    {
        return $this->hasMany(FirstAidItem::class);
    }
}
