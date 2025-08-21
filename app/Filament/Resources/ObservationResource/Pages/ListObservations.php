<?php

namespace App\Filament\Resources\ObservationResource\Pages;

use App\Filament\Resources\ObservationResource;
use App\Models\Observation;
use Filament\Pages\Actions\CreateAction;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ObservationsExport;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class ListObservations extends ListRecords
{
    protected static string $resource = ObservationResource::class;

    // ✅ ispravan potpis metode
    public function updatedTableFilters(): void
    {
        $this->dispatchBrowserEvent('refresh-header');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Novo zapažanje'),

            Action::make('export_pdf')
                ->label('Izvoz u PDF')
                ->icon('heroicon-s-download')
                ->color('warning')
                ->action(function () {
                    $observations = Observation::query()
                        ->whereYear('incident_date', request()->input('tableFilters.godina.value', now()->year))
                        ->get();

                    $pdf = Pdf::loadView('exports.observations-pdf', [
                        'observations' => $observations,
                    ])->setPaper('a4', 'landscape');

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'zapazanja.pdf'
                    );
                }),

            Action::make('export_excel')
                ->label('Izvoz u Excel')
                ->icon('heroicon-s-download')
                ->color('success')
                ->action(function () {
                    return response()->streamDownload(
                        fn () => print(
                            Excel::download(
                                new ObservationsExport,
                                'zapazanja.xlsx'
                            )->getFile()->getContent()
                        ),
                        'zapazanja.xlsx'
                    );
                }),
        ];
    }

    protected function getHeader(): ?View
{
    $filter = request()->input('tableFilters.godina_filter.value');
    $selectedYear = is_numeric($filter) ? intval($filter) : now()->year;

    $query = Observation::query()->whereYear('incident_date', $selectedYear);

    $ukupno = (clone $query)->count();
    $nijeZapoceto = (clone $query)->where('status', 'Not started')->count();
    $uTijeku = (clone $query)->where('status', 'In progress')->count();
    $zavrseno = (clone $query)->where('status', 'Complete')->count();

    return view('components.observations-header', [
        'selectedYear' => $selectedYear,
        'ukupno' => $ukupno,
        'nijeZapoceto' => $nijeZapoceto,
        'uTijeku' => $uTijeku,
        'zavrseno' => $zavrseno,
        'actions' => $this->getHeaderActions(),
    ]);
}
}