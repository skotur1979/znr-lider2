<?php

namespace App\Filament\Resources\DocumentationItemResource\Pages;

use App\Filament\Resources\DocumentationItemResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentationItem extends CreateRecord
{
    protected static string $resource = DocumentationItemResource::class;

    public function getTitle(): string
    {
        return 'Dodaj dokumentaciju';
    }
}
