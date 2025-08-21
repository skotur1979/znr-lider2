<?php

namespace App\Filament\Resources\ChemicalResource\Pages;

use App\Filament\Resources\ChemicalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Enums\HazardStatement;
use App\Enums\PrecautionaryStatement;

class EditChemical extends EditRecord
{
    protected static string $resource = ChemicalResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
protected function mutateFormDataBeforeFill(array $data): array
{
    $validH = array_keys(HazardStatement::list());
    $validP = array_keys(PrecautionaryStatement::list());

    $data['h_statements'] = collect($data['h_statements'] ?? [])
        ->filter(fn ($v) => in_array($v, $validH, true))
        ->values()->all();

    $data['p_statements'] = collect($data['p_statements'] ?? [])
        ->filter(fn ($v) => in_array($v, $validP, true))
        ->values()->all();

    return $data;
}
}
