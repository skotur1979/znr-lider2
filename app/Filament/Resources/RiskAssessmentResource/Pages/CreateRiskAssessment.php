<?php

namespace App\Filament\Resources\RiskAssessmentResource\Pages;

use App\Filament\Resources\RiskAssessmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRiskAssessment extends CreateRecord
{
    protected static string $resource = RiskAssessmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // ⬅️ uvijek postavi vlasnika
        return $data;
    }
}