<?php

namespace App\Filament\Resources\DocumentationItemResource\Pages;

namespace App\Filament\Resources\DocumentationItemResource\Pages;

use App\Filament\Resources\DocumentationItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentationItem extends CreateRecord
{
    protected static string $resource = DocumentationItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // uvijek postavi vlasnika
        return $data;
    }
}
