<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AvailableTestsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';
    protected static ?string $navigationLabel = 'Riješi testove';
    protected static ?string $navigationGroup = 'Testiranje';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.available-tests-page';
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && in_array(auth()->user()?->role, ['admin', 'korisnik']);
    }

    public function mount(): void
    {
        abort_unless(auth()->check() && in_array(auth()->user()->role, ['admin', 'korisnik']), 403);
    }
    public function getTitle(): string
{
    return 'Dostupni testovi';
}
}
