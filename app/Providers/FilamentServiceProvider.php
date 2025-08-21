<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Ovdje možeš dodavati globalne stilove, JS, teme, itd. ako bude potrebno
        Filament::serving(function () {
            // Nema ručne navigacije jer koristimo Filament Page klasu koja već sadrži sve
        });
    }
}