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

    // ⬇⬇⬇ OVIME 100% upisujemo vlasnika prije spremanja
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    // (opcionalno) dok sve ne profunkcionira, idi na listu umjesto /{record}/edit:
    // protected function getRedirectUrl(): string
    // {
    //     return static::getResource()::getUrl('index');
    // }
}