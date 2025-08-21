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
        $filters = request()->input('tableFilters', []);
        $selectedYear = $filters['godina']['value'] ?? null;

        $query = Expense::query()->with('budget');

        if ($selectedYear) {
            $query->whereHas('budget', fn ($q) => $q->where('godina', $selectedYear));
        }

        $ukupnoTroskova = $query->sum('iznos');
        $budget = $selectedYear
            ? Budget::where('godina', $selectedYear)->first()
            : null;
        $ukupniBudget = $budget?->ukupni_budget ?? 0;
        $razlika = $ukupniBudget - $ukupnoTroskova;

        $grupiraniTroskovi = $query
            ->whereNotNull('mjesec')
            ->selectRaw('mjesec, SUM(iznos) as ukupno')
            ->groupBy('mjesec')
            ->orderByRaw("FIELD(mjesec, 
                'Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj',
                'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac')")
            ->get();

        return view('filament.resources.expenses.partials.zbroj', [
            'godina' => $selectedYear ?? 'Sve',
            'ukupnoTroskova' => $ukupnoTroskova,
            'ukupniBudget' => $ukupniBudget,
            'razlika' => $razlika,
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
                    $filters = request()->input('tableFilters', []);
                    $selectedYear = $filters['godina']['value'] ?? null;

                    return Excel::download(
                        new ExpensesExport($selectedYear),
                        'Troskovi_' . ($selectedYear ?? 'sve') . '.xlsx'
                    );
                }),
        ];
    }
}