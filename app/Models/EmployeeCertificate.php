<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'title',
        'valid_from',
        'valid_until',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function certificates()
{
    return $this->hasMany(EmployeeCertificate::class);
}

}

