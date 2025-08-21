<?php

namespace App\Filament\Resources\FirstAidKitResource\Pages;

use App\Filament\Resources\FirstAidKitResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFirstAidKit extends EditRecord
{
    protected static string $resource = FirstAidKitResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
