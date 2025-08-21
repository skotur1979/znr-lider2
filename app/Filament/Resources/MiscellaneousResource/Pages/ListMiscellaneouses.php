<?php

namespace App\Filament\Resources\MiscellaneousResource\Pages;

use App\Filament\Resources\MiscellaneousResource;
use App\Models\Miscellaneous;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Fire;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\MiscellaneouesExport;
use App\Exports\MiscellaneousExport;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Filament\Pages\Actions\ButtonAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\Actions\Action;
use App\Imports\MiscellaneousImport;
use Livewire\TemporaryUploadedFile;

class ListMiscellaneouses extends ListRecords
{
    protected static string $resource = MiscellaneousResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Novo Ispitivanje'),
            Actions\Action::make('export_pdf')
                ->label('Izvoz u PDF')
                ->icon('heroicon-s-download')
                ->color('primary')
                ->action(function () {
                    $miscellaneouses = Miscellaneous::with('category')->get();
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.miscellaneouses', compact('miscellaneouses'));
    
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'Ispitivanja.pdf'
                    );
                }),
                /*Actions\Action::make('export_csv')
        ->label('Izvoz u CSV')
        ->icon('heroicon-s-table')
        ->color('secondary')
        ->action(function () {
            $miscellaneouses = Miscellaneous::with('category')->get();
    
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename=ostalo.csv',
            ];
    
            $callback = function () use ($miscellaneouses) {
                // UTF-8 BOM za Excel
                echo "\xEF\xBB\xBF";
    
                $handle = fopen('php://output', 'w');
    
                // Separator za hrvatski Excel
                $separator = ';';
    
                // Zaglavlja
                fputcsv($handle, [
                    'Naziv',
                    'Kategorija',
                    'Ispitao',
                    'Broj izvještaja',
                    'Vrijedi od',
                    'Vrijedi do',
                    'Lokacija',
                    'Napomena',
                ], $separator);
    
                foreach ($miscellaneouses as $miscellaneous) {
                    fputcsv($handle, [
                        $miscellaneous->name,
                        $miscellaneous->category->name,
                        $miscellaneous->examiner,
                        $miscellaneous->report_number,
                        optional($miscellaneous->examination_valid_from)->format('d.m.Y.'),
                        optional($miscellaneous->examination_valid_until)->format('d.m.Y.'),
                        $miscellaneous->location,
                        $miscellaneous->remark,
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
        return response()->streamDownload(
            fn () => print(Excel::download(new MiscellaneousExport, 'ostala_ispitivanja.xlsx')->getFile()->getContent()),
            'ostala_ispitivanja.xlsx'
        );
    }),
    Action::make('import_excel')
    ->label('Uvoz iz Excela')
    ->icon('heroicon-o-document-text')
    ->color('warning')
    ->form([
        \Filament\Forms\Components\FileUpload::make('file')
            ->label('Odaberi Excel datoteku')
            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
            ->required(),
    ])
    ->action(function (array $data) {
        Excel::import(new \App\Imports\MiscellaneousImport, $data['file']);
        \Filament\Notifications\Notification::make()
            ->title('Uspješan uvoz')
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
    
    
