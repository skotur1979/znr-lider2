<?php

namespace App\Filament\Resources\PersonalProtectiveEquipmentLogResource\Pages;

use App\Filament\Resources\PersonalProtectiveEquipmentLogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;


class ListPersonalProtectiveEquipmentLogs extends ListRecords
{
    protected static string $resource = PersonalProtectiveEquipmentLogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
