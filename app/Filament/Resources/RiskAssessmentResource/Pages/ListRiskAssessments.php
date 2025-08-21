<?php

namespace App\Filament\Resources\RiskAssessmentResource\Pages;

use App\Filament\Resources\RiskAssessmentResource;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;
use Filament\Pages\Actions\CreateAction;
use App\Models\RiskAssessment;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\DeleteBulkAction;

class ListRiskAssessments extends ListRecords
{
    protected static string $resource = RiskAssessmentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nova Procjena Rizika'),

            Actions\Action::make('export_pdf')
    ->label('Izvoz u PDF')
    ->icon('heroicon-s-download')
    ->color('warning')
    ->action(function () {
        $riskassessments = \App\Models\RiskAssessment::with(['participants', 'revisions', 'attachments'])->get();

        $pdf = Pdf::loadView('exports.risk-assessments-pdf', ['assessments' => $riskassessments]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'Procjene_Rizika.pdf'
        );
    }),
        ];
    }
    protected function getBulkActions(): array
{
    return [
        DeleteBulkAction::make()
    ->label('Trajno obriši')
    ->modalHeading('Trajno obriši Procjene rizika')
    ->modalDescription('Jeste li sigurni da želite trajno obrisati odabrane zapise?')
    
    ];
}
}
