<?php

namespace App\Filament\Resources\FireResource\Pages;

use App\Filament\Resources\FireResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFire extends EditRecord
{
    protected static string $resource = FireResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
