<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestAttemptResource\Pages;
use App\Models\TestAttempt;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TestAttemptResource extends Resource
{
    protected static ?string $model = TestAttempt::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Testiranje';
    protected static ?string $navigationLabel = 'Rješeni testovi';
    protected static ?string $pluralModelLabel = 'Rješeni testovi';
    protected static ?string $modelLabel = 'Rješeni test';
    protected static ?int $navigationSort = 96;

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function form(Form $form): Form { return $form->schema([]); }

    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery();
        if (! Auth::user()?->isAdmin()) {
            $q->where('user_id', Auth::id());
        }
        return $q->with(['user','test']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Korisnik'),
                TextColumn::make('test.naziv')->label('Naziv testa'),
                TextColumn::make('ime_prezime')->label('Ime i prezime'),
                TextColumn::make('radno_mjesto')->label('Radno mjesto'),
                TextColumn::make('datum_rodjenja')->date()->label('Datum rođenja'),
                TextColumn::make('bodovi_osvojeni')->label('Bodovi'),
                TextColumn::make('rezultat')->label('Rezultat (%)')->suffix('%'),
                BadgeColumn::make('prolaz')
                    ->label('Prolaz')
                    ->enum([ true => 'Da', false => 'Ne' ])   // tekst
                ->color(fn ($state) => $state ? 'success' : 'danger'),
    
                TextColumn::make('created_at')->dateTime('d.m.Y H:i')->label('Datum slanja'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
    ->url(fn (TestAttempt $record) => route('test-attempts.show', $record))
    ->openUrlInNewTab()
    ->label('Prikaži')
    ->icon('heroicon-o-eye')
            ]) // uklonjene akcije poput Edit
            ->bulkActions([]); // uklonjene bulk akcije poput Delete
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestAttempts::route('/'),
        ];
    }
    public static function getNavigationBadge(): ?string
{
    $q = static::getModel()::query();
    if (! auth()->user()?->isAdmin()) {
        $q->where('user_id', auth()->id());
    }
    return (string) $q->count();
}

}