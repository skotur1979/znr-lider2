<?php

namespace App\Filament\Resources\MedicalReferralResource\Pages;

use App\Filament\Resources\MedicalReferralResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MedicalReferral;
use Filament\Tables\Actions\Action;
use Filament\Tables;
use App\Services\Ra1PdfGenerator;

class ListMedicalReferrals extends ListRecords
{
    protected static string $resource = MedicalReferralResource::class;

    protected function getTableActions(): array
{
    return [
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make(),

        Action::make('export_single_pdf')
                ->label('Izvoz u PDF')
                ->icon('heroicon-s-download')
                ->color('secondary')
                ->action(function (MedicalReferral $record) {
                    $path     = Ra1PdfGenerator::generate($record);                 // spremi PDF na disk
                    $filename = Ra1PdfGenerator::buildFileName($record, 'd.m.Y.');   // naziv datoteke

                    return response()
                        ->download($path, $filename)
                        ->deleteFileAfterSend();
                }),
        ];
    }
}