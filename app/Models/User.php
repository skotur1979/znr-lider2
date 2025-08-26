<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name','email','password','is_admin'];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function canAccessFilament(): bool
    {
        // Svi aktivni korisnici imaju pristup panelu; prava dalje rješavaju policy + query filter
        return true;
    }

    // Relacije koje već koristiš
    public function employees()  { return $this->hasMany(Employee::class); }
    public function machines()   { return $this->hasMany(Machine::class); }
    public function fires()      { return $this->hasMany(Fire::class); }
    public function miscellaneouses() { return $this->hasMany(\App\Models\Miscellaneous::class); }
}
