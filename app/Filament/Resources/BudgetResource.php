<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Filament\Resources\BudgetResource\RelationManagers;
use App\Models\Budget;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ViewColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $pluralModelLabel = 'Budžet';

    protected static ?int $navigationSort = 99;

    protected static ?string $navigationGroup = 'Upravljanje';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('Unos budžeta')
                ->schema([
                    TextInput::make('godina')
                        ->label('Godina')
                        ->numeric()
                        ->required(),

                    TextInput::make('ukupni_budget')
                        ->label('Ukupni budžet (€)')
                        ->numeric()
                        ->required(),
                ]),
        ]);
}

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('godina')
                ->label('Godina')
                ->sortable(),

            TextColumn::make('ukupni_budget')
                ->label('Ukupni budžet (€)')
                ->formatStateUsing(fn ($state) => number_format($state, 2, ',', '.') . ' €')
                ->sortable(),

            ViewColumn::make('stanje_budgeta')
                ->label('Stanje budžeta')
                ->view('filament.tables.columns.budget-status'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
}

    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }    
}
