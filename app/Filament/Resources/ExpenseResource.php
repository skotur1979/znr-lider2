<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Moduli';
    protected static ?string $navigationLabel = 'Troškovi';
    protected static ?int $navigationSort = 9;

    public static function getFormSchema(): array
    {
        return [
            Section::make('Unos troška')
                ->schema([
                    Select::make('budget_id')
                        ->label('Godina')
                        ->required()
                        ->searchable()
                        ->options(fn () => \App\Models\Budget::orderByDesc('godina')->pluck('godina', 'id')),

                    TextInput::make('naziv_troska')
                        ->label('Naziv troška')
                        ->required(),

                    TextInput::make('iznos')
                        ->label('Ukupan trošak (€)')
                        ->numeric()
                        ->required(),

                    TextInput::make('dobavljac')
                        ->label('Dobavljač'),

                    Select::make('mjesec')
                        ->label('Mjesec')
                        ->options([
                            'Siječanj' => 'Siječanj',
                            'Veljača' => 'Veljača',
                            'Ožujak' => 'Ožujak',
                            'Travanj' => 'Travanj',
                            'Svibanj' => 'Svibanj',
                            'Lipanj' => 'Lipanj',
                            'Srpanj' => 'Srpanj',
                            'Kolovoz' => 'Kolovoz',
                            'Rujan' => 'Rujan',
                            'Listopad' => 'Listopad',
                            'Studeni' => 'Studeni',
                            'Prosinac' => 'Prosinac',
                        ])
                        ->required(),

                    Toggle::make('realizirano')
                        ->label('Realizirano'),
                ]),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budget.godina')
                    ->label('Godina')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('mjesec')
                    ->label('Mjesec')
                    ->sortable(),

                TextColumn::make('naziv_troska')
                    ->label('Naziv troška')
                    ->searchable(),

                TextColumn::make('iznos')
                    ->label('Iznos (€)')
                    ->formatStateUsing(fn ($state) => number_format($state, 2, ',', '.') . ' €')
                    ->sortable(),

                TextColumn::make('dobavljac')
                    ->label('Dobavljač')
                    ->searchable(),

                BooleanColumn::make('realizirano')
                    ->label('Realizirano')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->defaultSort('mjesec')
            ->filters([
                SelectFilter::make('mjesec')
                    ->label('Mjesec')
                    ->options([
                        'Siječanj' => 'Siječanj',
                        'Veljača' => 'Veljača',
                        'Ožujak' => 'Ožujak',
                        'Travanj' => 'Travanj',
                        'Svibanj' => 'Svibanj',
                        'Lipanj' => 'Lipanj',
                        'Srpanj' => 'Srpanj',
                        'Kolovoz' => 'Kolovoz',
                        'Rujan' => 'Rujan',
                        'Listopad' => 'Listopad',
                        'Studeni' => 'Studeni',
                        'Prosinac' => 'Prosinac',
                    ]),

                SelectFilter::make('godina')
                    ->label('Godina')
                    ->options(fn () => \App\Models\Budget::orderBy('godina')->pluck('godina', 'godina'))
                    ->placeholder('Sve')
                    ->default(null)
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null) {
                            return $query;
                        }

                        return $query->whereHas('budget', fn ($q) => $q->where('godina', $data['value']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
    ->label('Novi trošak')
    ->form(self::getFormSchema())
    ->modalHeading('Novi trošak'),

                Tables\Actions\Action::make('export_excel')
                    ->label('Izvoz u Excel')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->button()
                    ->action(fn () => \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExpensesExport, 'Troskovi.xlsx')),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    public static function getPluralLabel(): string
    {
        return 'Troškovi';
    }

    public static function getLabel(): string
    {
        return 'Trošak';
    }
}
