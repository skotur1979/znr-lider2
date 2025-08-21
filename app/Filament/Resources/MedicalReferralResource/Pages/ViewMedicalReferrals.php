<?php

namespace App\Filament\Resources\MedicalReferralResource\Pages;

use App\Filament\Resources\MedicalReferralResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\Ra1PdfGenerator; // <— DODANO

class ViewMedicalReferral extends ViewRecord
{
    protected static string $resource = MedicalReferralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Izvoz u PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('secondary')
                ->action(function () {
                    $referral = $this->record;

                    $pdf = Pdf::loadView('pdf.ra1', [
                        'referral' => $referral,
                        'employee' => $referral->employee,
                    ]);

                    // 👇 naziv: "Ime Prezime - RA-1 {broj} - {datum}.pdf"
                    $filename = Ra1PdfGenerator::buildFileName($referral, 'd.m.Y.'); // ili 'd.m.Y.' po želji

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        $filename
                    );
                }),
        ];
    }
}