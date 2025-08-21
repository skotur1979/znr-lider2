<?php

namespace App\Filament\Resources\PersonalProtectiveEquipmentLogResource\Pages;

use App\Filament\Resources\PersonalProtectiveEquipmentLogResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Pages\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PersonalProtectiveEquipmentExport;

class EditPersonalProtectiveEquipmentLog extends EditRecord
{
    protected static string $resource = PersonalProtectiveEquipmentLogResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Izvoz u PDF')
                ->icon('heroicon-o-document')
                ->color('primary')
                ->action(function () {
    $record = $this->record->load('items'); // ← bitno: učitaj i stavke

    $pdf = Pdf::loadView('exports.ozo-pdf', ['record' => $record]);

    $filename = 'OZO-' . $record->user_last_name . '-' . now()->format('d-m-Y') . '.pdf';

    return response()->streamDownload(fn () => print($pdf->output()), $filename);
}),


            Action::make('export_excel')
                ->label('Izvoz u Excel')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->action(function () {
                    $record = $this->record;
                    $filename = 'OZO-' . $record->user_last_name . '-' . now()->format('d-m-Y') . '.xlsx';

                    return Excel::download(new PersonalProtectiveEquipmentExport($record), $filename);
                }),
        ];
    }
}

