<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestResource\Pages;
use App\Filament\Resources\TestResource\RelationManagers;
use App\Models\Test;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Auth;
use App\Traits\AutoAssignsUser;

class TestResource extends Resource
{
    use AutoAssignsUser;

    protected static ?string $model = Test::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Testiranje';
    protected static ?string $navigationLabel = 'Testovi';
    protected static ?string $pluralModelLabel = 'Testovi';
    protected static ?string $modelLabel = 'Test';
    protected static ?int $navigationSort = 30; // da bude ispod Budžeta

    /** Forma kroz trait (hidden user_id + polja ispod) */
    public static function form(Form $form): Form
    {
        return static::assignUserField($form);
    }

    /** Polja koja traži trait */
    public static function additionalFormFields(): array
    {
        return [
        Section::make('Osnovni podaci')->schema([
                TextInput::make('naziv')->label('Naziv')->required(),
                TextInput::make('sifra')
                    ->label('Šifra')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('minimalni_prolaz')
                    ->label('Minimalni prolaz (%)')
                    ->numeric()
                    ->default(75),
                Textarea::make('opis')->label('Opis')->nullable(),
            ]),
        ];
    }

    /** Admin sve; ostali samo svoje */
    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery();
        if (! Auth::user()?->isAdmin()) {
            $q->where('user_id', Auth::id());
        }
        return $q;
    }

public static function table(Table $table): Table
{
    return $table->columns([
        TextColumn::make('naziv')->searchable(),
        TextColumn::make('sifra'),
        TextColumn::make('minimalni_prolaz')->label('Prolaz (%)'),
        TextColumn::make('created_at')->label('Dodano')->date(),
    ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTests::route('/'),
            'create' => Pages\CreateTest::route('/create'),
            'edit'   => Pages\EditTest::route('/{record}/edit'),
        ];
    }

    /** Badge u meniju u skladu sa scope-om */
    public static function getNavigationBadge(): ?string
    {
        $q = static::getModel()::query();
        if (! Auth::user()?->isAdmin()) {
            $q->where('user_id', Auth::id());
        }
        return (string) $q->count();
    }

    /** (opcionalno) global search da poštuje scope */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }
    
}