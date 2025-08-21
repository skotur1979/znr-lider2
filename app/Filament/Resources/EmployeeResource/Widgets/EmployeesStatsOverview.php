<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Employee;
use App\Models\Machine;
use App\Models\Fire;
use App\Models\Miscellaneous;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Carbon;
use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\MachineResource;
use App\Filament\Resources\MiscellaneousResource;

class EmployeesStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        // 1. Zaposlenici - liječnički
        $column1 = [
            Card::make('Zaposlenici', Employee::count())
                ->description('Ukupan broj')
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),

            Card::make('Zaposlenici', Employee::whereBetween('medical_examination_valid_until', [
                Carbon::today(), Carbon::today()->addMonth()
            ])->count())
                ->description('Liječnički uskoro ističe')
                ->descriptionIcon('heroicon-o-users')
                ->color('warning')
                ->url(EmployeeResource::getUrl('index', ['pregled' => 'uskoro']))
                ->extraAttributes(['style' => 'cursor: pointer']),

            Card::make('Zaposlenici', Employee::where('medical_examination_valid_until', '<', Carbon::today())->count())
                ->description('Liječnički istekao')
                ->descriptionIcon('heroicon-o-users')
                ->color('danger')
                ->url(EmployeeResource::getUrl('index', ['pregled' => 'isteklo']))
                ->extraAttributes(['style' => 'cursor: pointer']),
        ];

        // 2. Zaposlenici - ostali rokovi
        $column2 = [
            Card::make('Zaposlenici', Employee::count())
                ->description('Ukupan broj')
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),

            Card::make('Zaposlenici', Employee::where(function ($q) {
                $q->whereBetween('toxicology_valid_until', [Carbon::today(), Carbon::today()->addMonth()])
                  ->orWhereBetween('employers_authorization_valid_until', [Carbon::today(), Carbon::today()->addMonth()])
                  ->orWhereHas('certificates', function ($q2) {
                      $q2->whereBetween('valid_until', [Carbon::today(), Carbon::today()->addMonth()]);
                  });
            })->count())
                ->description('Ostali rokovi uskoro')
                ->descriptionIcon('heroicon-o-users')
                ->color('warning')
                ->url(EmployeeResource::getUrl('index', ['pregled' => 'ostalo-uskoro']))
                ->extraAttributes(['style' => 'cursor: pointer']),

            Card::make('Zaposlenici', Employee::where(function ($q) {
                $q->where('toxicology_valid_until', '<', Carbon::today())
                  ->orWhere('employers_authorization_valid_until', '<', Carbon::today())
                  ->orWhereHas('certificates', function ($q2) {
                      $q2->where('valid_until', '<', Carbon::today());
                  });
            })->count())
                ->description('Ostali rokovi istekli')
                ->descriptionIcon('heroicon-o-users')
                ->color('danger')
                ->url(EmployeeResource::getUrl('index', ['pregled' => 'ostalo-isteklo']))
                ->extraAttributes(['style' => 'cursor: pointer']),
        ];

        // 3. Strojevi
        $column3 = [
            Card::make('Strojevi', Machine::count())
                ->description('Ukupan broj')
                ->descriptionIcon('heroicon-o-cog')
                ->color('success'),

            Card::make('Strojevi', Machine::whereBetween('examination_valid_until', [
                Carbon::today(), Carbon::today()->addMonth()
            ])->count())
                ->description('Ispitivanje uskoro ističe')
                ->descriptionIcon('heroicon-o-cog')
                ->color('warning')
                ->url(MachineResource::getUrl('index', ['pregled' => 'uskoro']))
                ->extraAttributes(['style' => 'cursor: pointer']),

            Card::make('Strojevi', Machine::where('examination_valid_until', '<', Carbon::today())->count())
                ->description('Ispitivanje isteklo')
                ->descriptionIcon('heroicon-o-cog')
                ->color('danger')
                ->url(MachineResource::getUrl('index', ['pregled' => 'isteklo']))
                ->extraAttributes(['style' => 'cursor: pointer']),
        ];

        // 4. Ostalo (spojeni vatrogasni + ostalo)
        $combinedCount = Miscellaneous::count() + Fire::count();
        $combinedExpiring = Miscellaneous::whereBetween('examination_valid_until', [
                Carbon::today(), Carbon::today()->addMonth()
            ])->count()
            + Fire::whereBetween('examination_valid_until', [
                Carbon::today(), Carbon::today()->addMonth()
            ])->count();

        $combinedExpired = Miscellaneous::where('examination_valid_until', '<', Carbon::today())->count()
            + Fire::where('examination_valid_until', '<', Carbon::today())->count();

        $column4 = [
            Card::make('Ostala ispitivanja & Vatrogasni aparati', $combinedCount)
                ->description('Ukupan broj')
                ->descriptionIcon('heroicon-o-light-bulb')
                ->color('success'),

            Card::make('Ostala ispitivanja & Vatrogasni aparati', $combinedExpiring)
                ->description('Uskoro ističe')
                ->descriptionIcon('heroicon-o-light-bulb')
                ->color('warning')
                ->url(MiscellaneousResource::getUrl('index', ['pregled' => 'uskoro']))
                ->extraAttributes(['style' => 'cursor: pointer']),

            Card::make('Ostala ispitivanja & Vatrogasni aparati', $combinedExpired)
                ->description('Isteklo')
                ->descriptionIcon('heroicon-o-light-bulb')
                ->color('danger')
                ->url(MiscellaneousResource::getUrl('index', ['pregled' => 'isteklo']))
                ->extraAttributes(['style' => 'cursor: pointer']),
        ];

        // Vertikalno poravnanje (3 reda × 4 stupca)
        $cards = [];
        for ($i = 0; $i < 3; $i++) {
            $cards[] = $column1[$i];
            $cards[] = $column2[$i];
            $cards[] = $column3[$i];
            $cards[] = $column4[$i];
        }

        return $cards;
    }

    protected function getColumns(): int
    {
        return 4; // Četiri okomita stupca
    }
}
