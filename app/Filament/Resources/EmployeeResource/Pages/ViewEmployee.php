<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\EmployeeResource\Widgets\EmployeeReferralsWidget;
use Filament\Pages\Actions\Action;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('novaUputnica')
                ->label('Nova RA-1 uputnica')
                ->icon('heroicon-s-download')
                ->color('primary')
                ->url(fn () => \App\Filament\Resources\MedicalReferralResource::getUrl('create', [
                'employee_id' => $this->record->id,
            ])),
        ];
    }

    
    
}
