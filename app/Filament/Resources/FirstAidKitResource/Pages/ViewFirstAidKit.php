<?php

namespace App\Filament\Resources\FirstAidKitResource\Pages;

use App\Filament\Resources\FirstAidKitResource;
use Filament\Resources\Pages\ViewRecord;

class ViewFirstAidKit extends ViewRecord
{
    protected static string $resource = FirstAidKitResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Detalji ormarića prve pomoći';
    }

    public function getSubheading(): ?string
    {
        return 'Lokacija: ' . $this->record->location;
    }

    public function getContent(): \Illuminate\Contracts\View\View
    {
        return view('filament.resources.first-aid-kits.view', [
            'kit' => $this->record->load('items'),
        ]);
    }
}
