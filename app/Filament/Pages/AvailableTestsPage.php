<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Test;
use App\Models\TestAttempt;

class AvailableTestsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-check';
    protected static ?string $navigationLabel = 'Riješi testove';
    protected static ?string $navigationGroup = 'Testiranje';
    protected static ?int    $navigationSort  = 95;

    // ovaj blade samo sadrži <livewire:available-tests />
    protected static string $view = 'filament.pages.available-tests-page';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && in_array(Auth::user()?->role, ['admin', 'korisnik']);
    }

    public function mount(): void
    {
        abort_unless(Auth::check() && in_array(Auth::user()->role, ['admin', 'korisnik']), 403);
        // NEMA nikakvih queryja ovdje!
    }

    public function getTitle(): string
    {
        return 'Dostupni testovi';
    }

    // (opcionalno) badge: admin = ukupan broj testova, user = broj neriješenih
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (! $user) return null;

        $total = Test::count();

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return (string) $total;
        }

        $solved = TestAttempt::where('user_id', $user->id)
            ->distinct('test_id')
            ->count('test_id');

        return (string) max($total - $solved, 0);
    }
}

