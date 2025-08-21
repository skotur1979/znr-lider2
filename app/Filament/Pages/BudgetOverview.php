<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use Filament\Pages\Page;

class BudgetOverview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.budget-overview';
    protected static ?string $title = 'Pregled Budžeta';

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Disable navigation for this page
    }

    public static function route(): string
    {
        return '/budget-overview';
    }

    public $grupiraniTroskovi;

    public function mount(): void
    {
        $this->grupiraniTroskovi = Expense::selectRaw('YEAR(created_at) as godina, mjesec, SUM(iznos) as ukupno')
            ->whereNotNull('mjesec')
            ->groupByRaw('YEAR(created_at), mjesec')
            ->orderByRaw("YEAR(created_at), FIELD(mjesec, 
                'Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj',
                'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac')")
            ->get()
            ->groupBy('godina');
    }
}

