<?php

namespace App\Filament\Resources\IncidentResource\Pages;

use App\Filament\Resources\IncidentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\CreateAction;
use App\Models\Incident;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\IncidentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\View\View;


    class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

    protected function getHeader(): View
    {
        // Ispravno ime filtera: godina_filter
        $filter = request()->input('tableFilters.godina_filter.value');
        $selectedYear = is_numeric($filter) ? intval($filter) : now()->year;

        // Upit s filterom po godini (date_occurred)
        $incidenti = Incident::query()
            ->when($selectedYear, fn ($q) => $q->whereYear('date_occurred', $selectedYear));

        $ukupno = $incidenti->count();
        $lta = (clone $incidenti)->where('type_of_incident', 'LTA')->count();
        $mta = (clone $incidenti)->where('type_of_incident', 'MTA')->count();
        $faa = (clone $incidenti)->where('type_of_incident', 'FAA')->count();

        return view('exports.incidents-header', [
            'selectedYear' => $selectedYear,
            'ukupno' => $ukupno,
            'lta' => $lta,
            'mta' => $mta,
            'faa' => $faa,
            'actions' => $this->getCachedActions(),
        ]);
    }

    protected function getActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('export_pdf')
                ->label('Izvoz u PDF')
                ->icon('heroicon-s-download')
                ->color('warning')
                ->action(function () {
                    $incidents = Incident::all();
                    $pdf = Pdf::loadView('exports.incidents-pdf', compact('incidents'));
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'incidenti.pdf'
                    );
                }),

            Action::make('export_excel')
                ->label('Izvoz u Excel')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->action(function () {
                    return response()->streamDownload(
                        fn () => print(Excel::download(new IncidentsExport, 'incidenti.xlsx')->getFile()->getContent()),
                        'incidenti.xlsx'
                    );
                }),
        ];
    }
}