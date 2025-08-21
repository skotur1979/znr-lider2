<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalProtectiveEquipmentLog extends Model
{
    use HasFactory, SoftDeletes;

protected $fillable = [
        'user_last_name',
        'user_oib',
        'workplace',
        'organization_unit',
    ];

    public function items()
{
    return $this->hasMany(PersonalProtectiveEquipmentItem::class, 'personal_protective_equipment_log_id');
}
    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('edit', ['record' => $this->record]);
}
}