<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    // (opcionalno) auto-postavi user_id ako nije zadan
    protected static function booted(): void
    {
        static::creating(function (Category $model) {
            if (blank($model->user_id) && Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }

    public function user() { return $this->belongsTo(User::class); }

    public function miscellaneouses()
    {
        return $this->hasMany(\App\Models\Miscellaneous::class);
    }
}
