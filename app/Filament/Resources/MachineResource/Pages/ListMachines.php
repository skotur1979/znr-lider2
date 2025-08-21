<?php

namespace App\Filament\Resources\MachineResource\Pages;

use App\Filament\Resources\MachineResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Machine;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\MachinesExport;
use App\Exports\MachineExport;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Filament\Pages\Actions\ButtonAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\Actions\Action;
use Filament\Forms\Components\FileUpload;
use App\Imports\MachinesImport;
use Filament\Notifications\Notification;

class ListMachines extends ListRecords
{
    protected static string $resource = MachineResource::class;

    protected function getActions(): array
{
    return [
        Actions\CreateAction::make()->label('Nova Radna Oprema'),
        Actions\Action::make('export_pdf')
            ->label('Izvoz u PDF')
            ->icon('heroicon-s-download')
            ->color('primary')
            ->action(function () {
                $machines = \App\Models\Machine::all();
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.machines', compact('machines'));

                return response()->streamDownload(
                    fn () => print($pdf->output()),
                    'radna oprema.pdf'
                );
            }),
            /*Actions\Action::make('export_csv')
    ->label('Izvoz u CSV')
    ->icon('heroicon-s-table')
    ->color('secondary')
    ->action(function () {
        $machines = \App\Models\Machine::all();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=radna oprema.csv',
        ];

        $callback = function () use ($machines) {
            // UTF-8 BOM za Excel
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            // Separator za hrvatski Excel
            $separator = ';';

            // Zaglavlja
            fputcsv($handle, [
                'Naziv',
                'Proizvođač',
                'Tvornički broj',
                'Inventarni broj',
                'Vrijedi od',
                'Vrijedi do',
                'Lokacija',
                'Napomena',
            ], $separator);

            foreach ($machines as $machine) {
                fputcsv($handle, [
                    $machine->name,
                    $machine->manufacturer,
                    $machine->factory_number,
                    $machine->inventory_number,
                    optional($machine->examination_valid_from)->format('d.m.Y.'),
                    optional($machine->examination_valid_until)->format('d.m.Y.'),
                    $machine->location,
                    $machine->description,
                ], $separator);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }),*/
    Action::make('export_excel')
    ->label('Izvoz u Excel')
    ->icon('heroicon-o-document-text')
    ->color('success')
    ->action(function () {
        return Excel::download(new MachinesExport, 'radna_oprema.xlsx');
    }),
    Action::make('import_excel')
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
        Excel::import(new MachinesImport, $data['excel_file']);
        Notification::make()
            ->title('Uvoz uspješan!')
            ->success()
            ->send();
    }),
    ];
}
    
        protected function getTableQuery(): Builder
        {
            $query = parent::getTableQuery();
    
            $pregled = request()->get('pregled');
    
            if ($pregled === 'isteklo') {
                $query->where('examination_valid_until', '<', now());
            }
    
            if ($pregled === 'uskoro') {
                $query->whereBetween('examination_valid_until', [now(), now()->addMonth()]);
            }
    
            return $query;
        }
    }


