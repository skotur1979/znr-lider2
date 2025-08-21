<?php

namespace App\Filament\Resources\MiscellaneousResource\Pages;

use App\Filament\Resources\MiscellaneousResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMiscellaneous extends ViewRecord
{
    protected static string $resource = MiscellaneousResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
