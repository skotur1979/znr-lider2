<?php

namespace App\Filament\Resources\MiscellaneousResource\Pages;

use App\Filament\Resources\MiscellaneousResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMiscellaneous extends CreateRecord
{
    protected static string $resource = MiscellaneousResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
