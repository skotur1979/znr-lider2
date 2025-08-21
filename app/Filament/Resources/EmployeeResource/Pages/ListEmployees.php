<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Exports\EmployeesExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Pages\Actions\Action;
use Illuminate\Support\Facades\Storage;
use App\Imports\EmployeesImport;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ListEmployees extends ListRecords
{
    // Duplicate declaration removed

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $pregled = request()->get('pregled');

        if ($pregled === 'isteklo') {
            $query->where('medical_examination_valid_until', '<', Carbon::today());
        }

        if ($pregled === 'uskoro') {
            $query->whereBetween('medical_examination_valid_until', [Carbon::today(), Carbon::today()->addMonth()]);
        }

        if ($pregled === 'ostalo-uskoro') {
            $query->where(function ($q) {
                $q->whereBetween('toxicology_valid_until', [Carbon::today(), Carbon::today()->addMonth()])
                  ->orWhereBetween('employers_authorization_valid_until', [Carbon::today(), Carbon::today()->addMonth()])
                  ->orWhereHas('certificates', function ($q2) {
                      $q2->whereBetween('valid_until', [Carbon::today(), Carbon::today()->addMonth()]);
                  });
            });
        }

        if ($pregled === 'ostalo-isteklo') {
            $query->where(function ($q) {
                $q->where('toxicology_valid_until', '<', Carbon::today())
                  ->orWhere('employers_authorization_valid_until', '<', Carbon::today())
                  ->orWhereHas('certificates', function ($q2) {
                      $q2->where('valid_until', '<', Carbon::today());
                  });
            });
        }

    return $query;
}
    protected static string $resource = EmployeeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('export_pdf')
                ->label('Izvoz u PDF')
                ->icon('heroicon-s-download')
                ->color('primary')
                ->action(function () {
                    $employees = Employee::all();

                    $pdf = Pdf::loadView('pdf.employees', compact('employees'))
                        ->setPaper([0, 0, 5000, 842], 'landscape');

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'zaposlenici.pdf'
                    );
                }),
                

            /*Actions\Action::make('export_csv')
                ->label('Izvoz u CSV')
                ->icon('heroicon-s-table')
                ->color('secondary')
                ->action(function () {
                    $employees = Employee::all();

                    $headers = [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                        'Content-Disposition' => 'attachment; filename=zaposlenici.csv',
                    ];

                    $callback = function () use ($employees) {
                        echo "\xEF\xBB\xBF"; // UTF-8 BOM
                        $handle = fopen('php://output', 'w');
                        $separator = ';';

                        // Header row
                        fputcsv($handle, [
                            'Prezime i ime',
                            'Adresa',
                            'OIB',
                            'Telefon',
                            'Email',
                            'Radno mjesto',
                            'Datum zaposlenja',
                            'Liječnički vrijedi od',
                            'Liječnički vrijedi do',
                            'Članak 3.',
                            'Napomena',
                            'ZNR od',
                            'ZOP od',
                            'Izjava ZOP',
                            'Evakuacija od',
                            'Prva pomoć od',
                            'Toksikologija od',
                            'Toksikologija do',
                            'Zapaljivi od',
                            'Zapaljivi do',
                            'Ovlaštenje od',
                            'Ovlaštenje do',
                        ], $separator);

                        foreach ($employees as $e) {
                            fputcsv($handle, [
                                $e->name,
                                $e->address,
                                $e->OIB,
                                $e->phone,
                                $e->email,
                                $e->workplace,
                                optional($e->employeed_at)?->format('d.m.Y.'),
                                optional($e->medical_examination_valid_from)?->format('d.m.Y.'),
                                optional($e->medical_examination_valid_until)?->format('d.m.Y.'),
                                $e->article,
                                $e->remark,
                                optional($e->occupational_safety_valid_from)?->format('d.m.Y.'),
                                optional($e->fire_protection_valid_from)?->format('d.m.Y.'),
                                optional($e->fire_protection_statement_at)?->format('d.m.Y.'),
                                optional($e->evacuation_valid_from)?->format('d.m.Y.'),
                                optional($e->first_aid_valid_from)?->format('d.m.Y.'),
                                optional($e->toxicology_valid_from)?->format('d.m.Y.'),
                                optional($e->toxicology_valid_until)?->format('d.m.Y.'),
                                optional($e->handling_flammable_materials_valid_from)?->format('d.m.Y.'),
                                optional($e->handling_flammable_materials_valid_until)?->format('d.m.Y.'),
                                optional($e->employers_authorization_valid_from)?->format('d.m.Y.'),
                                optional($e->employers_authorization_valid_until)?->format('d.m.Y.'),
                            ], $separator);
                        }

                        fclose($handle);
                    };

                    return response()->stream($callback, 200, $headers);
                }),*/

            Actions\Action::make('export_excel')
    ->label('Izvoz u Excel')
    ->icon('heroicon-o-document-text')
    ->color('success')
    ->action(function () {
        return Excel::download(new EmployeesExport, 'zaposlenici.xlsx');
    }),
    
    Actions\Action::make('import_excel')
            ->label('Uvoz iz Excela')
            ->icon('heroicon-o-document-text')
            ->color('warning')
            ->form([
                FileUpload::make('excel_file')
                    ->label('Excel datoteka')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->required(),
            ])
            ->action(function (array $data) {
                Excel::import(new EmployeesImport, $data['excel_file']);
                Notification::make()
                    ->title('Uvoz uspješan!')
                    ->success()
                    ->send();
            }),
            
    ];
}

}

 