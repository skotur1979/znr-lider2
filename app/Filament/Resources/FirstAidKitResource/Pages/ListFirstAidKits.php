<?php

namespace App\Filament\Resources\FirstAidKitResource\Pages;

use App\Filament\Resources\FirstAidKitResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\CreateAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Pages\Actions\Action;
use App\Exports\FirstAidKitsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FirstAidKitItemsExport;

class ListFirstAidKits extends ListRecords
{
    protected static string $resource = FirstAidKitResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make()->label('Nova Prva Pomoć'),

        Action::make('export_pdf')
            ->label('Izvoz svih u PDF')
            ->icon('heroicon-s-download')
            ->color('warning')
            ->action(function () {
                $kits = \App\Models\FirstAidKit::with('items')->get();

                $pdf = Pdf::loadView('exports.all-first-aid-kits', [
                    'kits' => $kits,
                ])->setPaper('a4');

                return response()->streamDownload(
                    fn () => print($pdf->output()),
                    'svi_ormarici_prve_pomoci.pdf'
                );
            }),

        Action::make('export_excel')
            ->label('Izvoz svih u Excel')
            ->icon('heroicon-o-document-text')
            ->color('success')
            ->action(function () {
                return response()->streamDownload(
                    fn () => print(Excel::download(new FirstAidKitsExport, 'prva_pomoc.xlsx')->getFile()->getContent()),
                    'prva_pomoc.xlsx'
                );
            }),
        ];
    }
}