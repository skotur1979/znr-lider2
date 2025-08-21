<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role', // obavezno uključeno
    ];
  
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function canAccessFilament(): bool
{
    return in_array($this->role, ['admin', 'korisnik']);
}


    public function rules()
{
    return [
        'email' => [
            'required',
            'email',
            Rule::unique('users')->ignore($this->route('user')),
        ],
    ];
}
public function employees()
{
    return $this->hasMany(Employee::class);
}
public function machines()
{
    return $this->hasMany(Machine::class);
}
public function fires()
{
    return $this->hasMany(Fire::class);
}
public function miscellaneouses()
{
    return $this->hasMany(\App\Models\Miscellaneous::class);


}
public function isAdmin(): bool
{
    return $this->role === 'admin';
}

}