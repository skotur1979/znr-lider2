<?php

namespace App\Filament\Resources\PersonalProtectiveEquipmentLogResource\Pages;

use App\Filament\Resources\PersonalProtectiveEquipmentLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePersonalProtectiveEquipmentLog extends CreateRecord
{
    protected static string $resource = PersonalProtectiveEquipmentLogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // uvijek postavi vlasnika
        return $data;
    }
}
