<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\Budget;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Filament\Pages\Actions\Action;
use App\Exports\ExpensesExport;
use Maatwebsite\Excel\Facades\Excel;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeader(): ?View
    {
        // Godina iz table filtera (?tableFilters[godina][value]=YYYY)
        $selectedYear = request('tableFilters.godina.value');

        $isAdmin = auth()->user()?->isAdmin();
        $userId  = auth()->id();

        // Base upit (SAMO realizirano) + user scope + godina
        $base = Expense::query()
            ->where('realizirano', true)
            ->when(!$isAdmin, fn ($q) => $q->where('user_id', $userId))
            ->when($selectedYear, fn ($q) =>
                $q->whereHas('budget', fn ($b) => $b->where('godina', $selectedYear))
            );

        // Ukupno realiziranih troškova
        $ukupnoTroskova = (clone $base)->sum('iznos');

        // Ukupni budžet (možda ih je više za istu godinu → zbroji)
        $ukupniBudget = Budget::query()
            ->when(!$isAdmin, fn ($q) => $q->where('user_id', $userId))
            ->when($selectedYear, fn ($q) => $q->where('godina', $selectedYear))
            ->sum('ukupni_budget');

        $razlika = $ukupniBudget - $ukupnoTroskova;

        // Grupiranje po mjesecima (SAMO realizirano)
        $grupiraniTroskovi = (clone $base)
            ->whereNotNull('mjesec')
            ->selectRaw('mjesec, SUM(iznos) as ukupno')
            ->groupBy('mjesec')
            ->orderByRaw("FIELD(mjesec,
                'Siječanj','Veljača','Ožujak','Travanj','Svibanj','Lipanj',
                'Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac')")
            ->get();

        return view('filament.resources.expenses.partials.zbroj', [
            'godina'            => $selectedYear ?? 'Sve',
            'ukupnoTroskova'    => (float) $ukupnoTroskova,
            'ukupniBudget'      => (float) $ukupniBudget,
            'razlika'           => (float) $razlika,
            'grupiraniTroskovi' => $grupiraniTroskovi,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Izvoz u Excel')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->button()
                ->action(function () {
                    $selectedYear = request('tableFilters.godina.value');
                    return Excel::download(
                        new ExpensesExport($selectedYear),
                        'Troskovi_' . ($selectedYear ?? 'sve') . '.xlsx'
                    );
                }),
        ];
    }
}
