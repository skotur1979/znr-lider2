<?php

namespace App\Filament\Resources\MedicalReferralResource\Pages;

use App\Filament\Resources\MedicalReferralResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMedicalReferral extends EditRecord
{
    protected static string $resource = MedicalReferralResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeCreate(array $data): array
{
    if (($data['manual_entry'] ?? false) === true) {
        // ručni unos → ne vežemo record na employee
        $data['employee_id'] = null;
        return $data;
    }

    if (!empty($data['employee_id'])) {
        $emp = \App\Models\Employee::find($data['employee_id']);
        if ($emp) {
            $data['full_name']       = $data['full_name']       ?? $emp->name;
            $data['oib']             = $data['oib']             ?? $emp->OIB;
            $data['job_title']       = $data['job_title']       ?: ($emp->job_title ?? null);
            $data['education']       = $data['education']       ?: ($emp->education ?? null);
            $data['place_of_birth']  = $data['place_of_birth']  ?: ($emp->place_of_birth ?? null);
            $data['name_of_parents'] = $data['name_of_parents'] ?: ($emp->name_of_parents ?? null);
        }
    }

    return $data;
}
}
