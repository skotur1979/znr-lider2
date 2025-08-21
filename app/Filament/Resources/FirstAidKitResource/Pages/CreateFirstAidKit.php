<?php

namespace App\Filament\Resources\FirstAidKitResource\Pages;

use App\Filament\Resources\FirstAidKitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFirstAidKit extends CreateRecord
{
    protected static string $resource = FirstAidKitResource::class;

    public function getTitle(): string
    {
        return 'Nova Prva Pomoć';
    }
}