<?php

namespace App\Filament\Resources\FireResource\Pages;

use App\Filament\Resources\FireResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFire extends ViewRecord
{
    protected static string $resource = FireResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}