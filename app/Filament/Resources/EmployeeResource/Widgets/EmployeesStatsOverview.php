<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Employee;
use App\Models\Machine;
use App\Models\Miscellaneous;
use App\Models\Fire;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Carbon;

use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\MachineResource;
use App\Filament\Resources\MiscellaneousResource;
use App\Filament\Resources\FireResource;

class EmployeesStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $today   = Carbon::today();
        $inMonth = Carbon::today()->addMonth();

        // Helper: uzmi isti upit kao na indexu resursa
        $qEmployees = method_exists(EmployeeResource::class, 'getEloquentQuery')
            ? EmployeeResource::getEloquentQuery()
            : Employee::query();

        $qMachines = method_exists(MachineResource::class, 'getEloquentQuery')
            ? MachineResource::getEloquentQuery()
            : Machine::query();

        $qMisc = method_exists(MiscellaneousResource::class, 'getEloquentQuery')
            ? MiscellaneousResource::getEloquentQuery()
            : Miscellaneous::query();

        $qFire = class_exists(FireResource::class) && method_exists(FireResource::class, 'getEloquentQuery')
            ? FireResource::getEloquentQuery()
            : Fire::query();

        // 👇 helper koji svim builderima doda "active = 1" i makne trashed, ako postoji
        $purify = function ($q) {
            // isključi deaktivirane (ako kolona postoji; u većini tvojih modula je 'active')
            $q->when($this->columnExists($q, 'active'), fn ($qq) => $qq->where('active', true));

            // isključi soft-deleted (ako model koristi SoftDeletes)
            if ($this->usesSoftDeletes($q)) {
                $q->withoutTrashed();
            }
            return $q;
        };

        $qEmployees = $purify($qEmployees);
        $qMachines  = $purify($qMachines);
        $qMisc      = $purify($qMisc);
        $qFire      = $purify($qFire);

        /* ---------------- ZAPOSLENICI – liječnički ---------------- */
        $empTotal   = (clone $qEmployees)->count();
        $empSoon    = (clone $qEmployees)->whereBetween('medical_examination_valid_until', [$today, $inMonth])->count();
        $empExpired = (clone $qEmployees)->whereDate('medical_examination_valid_until', '<', $today)->count();

        $column1 = [
            Card::make('Zaposlenici', $empTotal)
                ->description('Ukupan broj')
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),

            Card::make('Zaposlenici', $empSoon)
                ->description('Liječnički uskoro ističe')
                ->descriptionIcon('heroicon-o-users')
                ->color('warning')
                ->url(EmployeeResource::getUrl('index', ['pregled' => 'uskoro']))
                ->extraAttributes(['style' => 'cursor: pointer']),

            Card::make('Zaposlenici', $empExpired)
                ->description('Liječnički istekao')
                ->descriptionIcon('heroicon-o-users')
                ->color('danger')
                ->url(EmployeeResource::getUrl('index', ['pregled' => 'isteklo']))
                ->extraAttributes(['style' => 'cursor: pointer']),
        ];

        /* ---------------- ZAPOSLENICI – ostali rokovi ---------------- */
        $empOtherTotal   = (clone $qEmployees)->count();
        $empOtherSoon    = (clone $qEmployees)->where(function ($q) use ($today, $inMonth) {
                                $q->whereBetween('toxicology_valid_until', [$today, $inMonth])
                                  ->orWhereBetween('employers_authorization_valid_until', [$today, $inMonth])
                                  ->orWhereHas('certificates', fn($qq) => $qq->whereBetween('valid_until', [$today, $inMonth]));
                            })->count();
        $empOtherExpired = (clone $qEmployees)->where(function ($q) use ($today) {
                                $q->whereDate('toxicology_valid_until', '<', $today)
                                  ->orWhereDate('employers_authorization_valid_until', '<', $today)
                                  ->orWhereHas('certificates', fn($qq) => $qq->whereDate('valid_until', '<', $today));
                            })->count();

        $column2 = [
            Card::make('Zaposlenici', $empOtherTotal)
                ->description('Ukupan broj')
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),

            Card::make('Zaposlenici', $empOtherSoon)
                ->description('Ostali rokovi uskoro')
                ->descriptionIcon('heroicon-o-users')
                ->color('warning')
                ->url(EmployeeResource::getUrl('index', ['pregled' => 'ostalo-uskoro']))
                ->extraAttributes(['style' => 'cursor: pointer']),

            Card::make('Zaposlenici', $empOtherExpired)
                ->description('Ostali rokovi istekli')
                ->descriptionIcon('heroicon-o-users')
                ->color('danger')
                ->url(EmployeeResource::getUrl('index', ['pregled' => 'ostalo-isteklo']))
                ->extraAttributes(['style' => 'cursor: pointer']),
        ];

        /* ---------------- STROJEVI ---------------- */
        $machTotal   = (clone $qMachines)->count();
        $machSoon    = (clone $qMachines)->whereBetween('examination_valid_until', [$today, $inMonth])->count();
        $machExpired = (clone $qMachines)->whereDate('examination_valid_until', '<', $today)->count();

        $column3 = [
            Card::make('Strojevi', $machTotal)
                ->description('Ukupan broj')
                ->descriptionIcon('heroicon-o-cog')
                ->color('success'),

            Card::make('Strojevi', $machSoon)
                ->description('Ispitivanje uskoro ističe')
                ->descriptionIcon('heroicon-o-cog')
                ->color('warning')
                ->url(MachineResource::getUrl('index', ['pregled' => 'uskoro']))
                ->extraAttributes(['style' => 'cursor: pointer']),

            Card::make('Strojevi', $machExpired)
                ->description('Ispitivanje isteklo')
                ->descriptionIcon('heroicon-o-cog')
                ->color('danger')
                ->url(MachineResource::getUrl('index', ['pregled' => 'isteklo']))
                ->extraAttributes(['style' => 'cursor: pointer']),
        ];

        /* ---------------- OSTALA ISPITIVANJA + VATROGASNI ---------------- */
        $miscTotal   = (clone $qMisc)->count();
        $miscSoon    = (clone $qMisc)->whereBetween('examination_valid_until', [$today, $inMonth])->count();
        $miscExpired = (clone $qMisc)->whereDate('examination_valid_until', '<', $today)->count();

        $fireTotal   = (clone $qFire)->count();
        $fireSoon    = (clone $qFire)->whereBetween('examination_valid_until', [$today, $inMonth])->count();
        $fireExpired = (clone $qFire)->whereDate('examination_valid_until', '<', $today)->count();

        $column4 = [
            Card::make('Ostala ispitivanja & Vatrogasni aparati', $miscTotal + $fireTotal)
                ->description('Ukupan broj')
                ->descriptionIcon('heroicon-o-light-bulb')
                ->color('success'),

            Card::make('Ostala ispitivanja & Vatrogasni aparati', $miscSoon + $fireSoon)
                ->description('Uskoro ističe')
                ->descriptionIcon('heroicon-o-light-bulb')
                ->color('warning')
                ->url(MiscellaneousResource::getUrl('index', ['pregled' => 'uskoro']))
                ->extraAttributes(['style' => 'cursor: pointer']),

            Card::make('Ostala ispitivanja & Vatrogasni aparati', $miscExpired + $fireExpired)
                ->description('Isteklo')
                ->descriptionIcon('heroicon-o-light-bulb')
                ->color('danger')
                ->url(MiscellaneousResource::getUrl('index', ['pregled' => 'isteklo']))
                ->extraAttributes(['style' => 'cursor: pointer']),
        ];

        // 3 reda × 4 stupca
        $cards = [];
        for ($i = 0; $i < 3; $i++) {
            $cards[] = $column1[$i];
            $cards[] = $column2[$i];
            $cards[] = $column3[$i];
            $cards[] = $column4[$i];
        }

        return $cards;
    }

    /* ===== Helpers ===== */

    private function usesSoftDeletes($q): bool
    {
        return in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($q->getModel()));
    }

    private function columnExists($q, string $column): bool
    {
        // brza provjera preko liste fillable/casts/attributes; dovoljno za "active"
        $model = $q->getModel();
        return in_array($column, $model->getFillable(), true)
            || array_key_exists($column, $model->getCasts())
            || array_key_exists($column, $model->getAttributes())
            || \Schema::hasColumn($model->getTable(), $column);
    }
}
