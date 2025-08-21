<?php

namespace App\Filament\Resources\ChemicalResource\Pages;

use App\Filament\Resources\ChemicalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Chemical;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\File;
use App\Exports\ChemicalFormattedExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ChemicalImport;
use Livewire\TemporaryUploadedFile;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;


class ListChemicals extends ListRecords
{
    protected static string $resource = ChemicalResource::class;

    protected function getActions(): array
{
    return [
        Actions\CreateAction::make()->label('Nova Kemikalija'),

        Action::make('export_pdf')
            ->label('Izvoz u PDF')
            ->icon('heroicon-s-download')
            ->color('warning')
            ->action(function () {
                $chemicals = Chemical::all();
                $pdf = Pdf::loadView('exports.chemicals-pdf', compact('chemicals'));
                return response()->streamDownload(
                    fn () => print($pdf->output()),
                    'kemikalije.pdf'
                );
            }),

        Action::make('export_excel')
                ->label('Izvoz u Excel')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->action(function () {
                    return Excel::download(new ChemicalFormattedExport, 'Kemikalije.xlsx');
                }),
                Action::make('import_excel')
    ->label('Uvoz iz Excela')
    ->icon('heroicon-o-document-text')
    ->color('primary')
    ->form([
        FileUpload::make('excel_file')
            ->label('Odaberi Excel datoteku')
            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
            ->required()
            ->storeFiles(false),
    ])
    ->action(function (array $data): void {
        /** @var TemporaryUploadedFile $file */
        $file = $data['excel_file'];
        Excel::import(new ChemicalImport, $file->getRealPath());
        Filament\Notifications\Notification::make()
            ->title('Uvoz uspješan')
            ->success()
            ->send();
    }),
        ];
    }
}