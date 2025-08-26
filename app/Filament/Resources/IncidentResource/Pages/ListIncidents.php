<?php

namespace App\Filament\Resources\IncidentResource\Pages;

use App\Filament\Resources\IncidentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\CreateAction;
use App\Models\Incident;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

    /** Builda OSNOVNI upit s istim filterima koje koristi UI (godina + vlasnik) */
    protected function baseQuery()
    {
        // Godina iz SelectFilter::make('godina_filter')
        $year = request()->input('tableFilters.godina_filter.value');
        $year = is_numeric($year) ? (int) $year : now()->year;

        $q = Incident::query()->withoutTrashed()
            ->when($year, fn ($qq) => $qq->whereYear('date_occurred', $year));

        // Admin vidi sve, korisnik samo svoje
        if (! Auth::user()?->isAdmin()) {
            $q->where('user_id', Auth::id());
        }

        return [$q, $year];
    }

    protected function getHeader(): View
    {
        [$incidenti, $selectedYear] = $this->baseQuery();

        $ukupno = (clone $incidenti)->count();
        $lta    = (clone $incidenti)->where('type_of_incident', 'LTA')->count();
        $mta    = (clone $incidenti)->where('type_of_incident', 'MTA')->count();
        $faa    = (clone $incidenti)->where('type_of_incident', 'FAA')->count();

        return view('exports.incidents-header', [
            'selectedYear' => $selectedYear,
            'ukupno'       => $ukupno,
            'lta'          => $lta,
            'mta'          => $mta,
            'faa'          => $faa,
            'actions'      => $this->getCachedActions(),
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
                    [$q] = $this->baseQuery();
                    $incidents = $q->orderBy('date_occurred', 'desc')->get();

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
                    [$q] = $this->baseQuery();
                    $rows = $q->orderBy('date_occurred', 'desc')->get([
                        'location',
                        'type_of_incident',
                        'permanent_or_temporary',
                        'date_occurred',
                        'date_of_return',
                        'working_days_lost',
                        'causes_of_injury',
                        'accident_injury_type',
                        'injured_body_part',
                        'other',
                    ])->map(function ($i) {
                        return [
                            'Lokacija'            => $i->location,
                            'Vrsta incidenta'     => $i->type_of_incident,
                            'Vrsta zaposlenja'    => $i->permanent_or_temporary,
                            'Datum nastanka'      => optional($i->date_occurred)->format('Y-m-d'),
                            'Datum povratka'      => optional($i->date_of_return)->format('Y-m-d'),
                            'Izgubljeni dani'     => $i->working_days_lost,
                            'Uzrok'               => $i->causes_of_injury,
                            'Tip ozljede'         => $i->accident_injury_type,
                            'Ozlijeđeni dio'      => $i->injured_body_part,
                            'Napomena'            => $i->other,
                        ];
                    });

                    return response()->streamDownload(function () use ($rows) {
                        SimpleExcelWriter::streamDownload('incidenti.xlsx')
                            ->addRows($rows->toArray())
                            ->toBrowser();
                    }, 'incidenti.xlsx');
                }),
        ];
    }
}
