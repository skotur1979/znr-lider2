<?php

namespace App\Filament\Resources\ChemicalResource\Pages;

use App\Filament\Resources\ChemicalResource;
use Filament\Resources\Pages\CreateRecord;
use App\Enums\HazardStatement;
use App\Enums\PrecautionaryStatement;

class CreateChemical extends CreateRecord
{
    protected static string $resource = ChemicalResource::class;

    /**
     * Nakon uspješnog kreiranja vrati korisnika na listu.
     * (izbjegavamo redirect na /edit koji ti je davao 404 kad zapis nije vidljiv u queryju)
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    /**
     * Sanitiziraj podatke prije spremanja (H/P oznake moraju biti u dozvoljenim vrijednostima).
     * Po želji osiguraj i user_id (ne smeta i ako to već radiš u Resource-u).
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $validH = array_keys(HazardStatement::list());
        $validP = array_keys(PrecautionaryStatement::list());

        $data['h_statements'] = array_values(array_intersect(
            (array) ($data['h_statements'] ?? []),
            $validH
        ));

        $data['p_statements'] = array_values(array_intersect(
            (array) ($data['p_statements'] ?? []),
            $validP
        ));

        // safety-net; možeš ostaviti ili maknuti ako već radiš u Resource-u
        $data['user_id'] = $data['user_id'] ?? auth()->id();

        return $data;
    }
}
