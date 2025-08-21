<?php

namespace App\Filament\Resources\FireResource\Pages;

use App\Filament\Resources\FireResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Fire;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\FiresExport;
use App\Exports\FireExport;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Filament\Pages\Actions\ButtonAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use App\Imports\FireImport;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;

class ListFires extends ListRecords
{
    protected static string $resource = FireResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export_pdf')
                ->label('Izvoz u PDF')
                ->icon('heroicon-s-download')
                ->color('primary')
                ->action(function () {
                    $fires = \App\Models\Fire::all();
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.fires', compact('fires'));
    
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'vatrogasni aparati.pdf'
                    );
                }),
                /*Actions\Action::make('export_csv')
        ->label('Izvoz u CSV')
        ->icon('heroicon-s-table')
        ->color('secondary')
        ->action(function () {
            $fires = \App\Models\Fire::all();
    
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename=Vatrogasni aparati.csv',
            ];
    
            $callback = function () use ($fires) {
                // UTF-8 BOM za Excel
                echo "\xEF\xBB\xBF";
    
                $handle = fopen('php://output', 'w');
    
                // Separator za hrvatski Excel
                $separator = ';';
    
                // Zaglavlja
                fputcsv($handle, [
                    'Mjesto gdje se aparat nalazi',
                    'Tip aparata',
                    'Tvor.broj/Godina proizv.',
                    'Serijski broj evidencijske naljepnice',
                    'Datum periodičkog servisa ',
                    'Vrijedi do',
                    'Naziv servisera',
                    'Datum redovnog pregleda',
                    'Uočljivost i dostupnost aparata',
                    'Uočeni nedostatci',
                    'Postupci otklanjanja',
                ], $separator);
    
                foreach ($fires as $fire) {
                    fputcsv($handle, [
                        $fire->place,
                        $fire->type,
                        $fire->getAttribute('factory_number/year_of_production'),
                        $fire->serial_label_number,
                        optional($fire->examination_valid_from)->format('d.m.Y.'),
                        optional($fire->examination_valid_until)->format('d.m.Y.'),
                        $fire->service,
                        optional($fire->regular_examination_valid_from)->format('d.m.Y.'),
                        $fire->visible,
                        $fire->remark,
                        $fire->action,
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
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\FiresExport,
                'Vatrogasni_aparati.xlsx'
            );
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
                Excel::import(new FireImport, $data['excel_file']);
    
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
