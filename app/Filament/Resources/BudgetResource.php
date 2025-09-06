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
use Illuminate\Support\Facades\Auth;
use App\Traits\AutoAssignsUser;

class BudgetResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $pluralModelLabel = 'Budžet';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationGroup = 'Upravljanje';

    /** Forma ide kroz trait (hidden user_id + ova polja) */
    public static function form(Form $form): Form
    {
        return static::assignUserField($form);
    }

    /** Polja koja traži trait */
    public static function additionalFormFields(): array
    {
        return [
            Section::make('Unos budžeta')->schema([
                TextInput::make('godina')->label('Godina')->numeric()->required(),
                TextInput::make('ukupni_budget')->label('Ukupni budžet (€)')->numeric()->required(),
            ]),
        ];
    }

    /** Admin vidi sve; ostali samo svoje */
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
        return $table
            ->columns([
                TextColumn::make('godina')
                    ->label('Godina')
                    ->sortable(),

                TextColumn::make('ukupni_budget')
                    ->label('Ukupni budžet (€)')
                    ->formatStateUsing(function ($state) {
                        $n = is_numeric($state) ? (float) $state : 0;
                        return number_format($n, 2, ',', '.') . ' €';
                    })
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit'   => Pages\EditBudget::route('/{record}/edit'),
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