<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FirstAidItem extends Model
{
    use HasFactory;

    protected $fillable = ['first_aid_kit_id', 'material_type', 'purpose', 'valid_until'];

    public function kit()
    {
        return $this->belongsTo(FirstAidKit::class, 'first_aid_kit_id');
    }
    public function firstAidKit()
{
    return $this->belongsTo(\App\Models\FirstAidKit::class);
}
}
