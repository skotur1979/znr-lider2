<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use App\Models\{Expense, Budget};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Traits\AutoAssignsUser; // ako koristiš trait kao u ostalim modulima

class ExpenseResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Upravljanje';
    protected static ?string $navigationLabel = 'Troškovi';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        // trait ubacuje hidden user_id + polja iz additionalFormFields()
        return static::assignUserField($form);
    }

    public static function additionalFormFields(): array
    {
        return [
            Section::make('Unos troška')->schema([
                Select::make('budget_id')
                    ->label('Godina')
                    ->required()
                    ->searchable()
                    ->options(function () {
                        $qb = Budget::query()->orderByDesc('godina');
                        if (! Auth::user()?->isAdmin()) {
                            $qb->where('user_id', Auth::id());
                        }
                        return $qb->pluck('godina', 'id');
                    }),

                TextInput::make('naziv_troska')->label('Naziv troška')->required(),
                TextInput::make('iznos')->label('Ukupan trošak (€)')->numeric()->required(),
                TextInput::make('dobavljac')->label('Dobavljač'),

                Select::make('mjesec')->label('Mjesec')->options([
                    'Siječanj'=>'Siječanj','Veljača'=>'Veljača','Ožujak'=>'Ožujak','Travanj'=>'Travanj',
                    'Svibanj'=>'Svibanj','Lipanj'=>'Lipanj','Srpanj'=>'Srpanj','Kolovoz'=>'Kolovoz',
                    'Rujan'=>'Rujan','Listopad'=>'Listopad','Studeni'=>'Studeni','Prosinac'=>'Prosinac',
                ])->required(),

                Toggle::make('realizirano')->label('Realizirano'),
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
        return $table
            ->columns([
                TextColumn::make('budget.godina')->label('Godina')->sortable()->searchable(),
                TextColumn::make('mjesec')->label('Mjesec')->sortable(),
                TextColumn::make('naziv_troska')->label('Naziv troška')->searchable(),
                TextColumn::make('iznos')
                ->label('Iznos (€)')
                ->formatStateUsing(function ($state) {
                $n = is_numeric($state) ? (float) $state : 0;
                return number_format($n, 2, ',', '.') . ' €';
                })
                ->sortable(),
                TextColumn::make('dobavljac')->label('Dobavljač')->searchable(),
                BooleanColumn::make('realizirano')->label('Realizirano')
                    ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle'),
            ])
            ->defaultSort('mjesec')
            ->filters([
                SelectFilter::make('mjesec')->label('Mjesec')->options([
                    'Siječanj'=>'Siječanj','Veljača'=>'Veljača','Ožujak'=>'Ožujak','Travanj'=>'Travanj',
                    'Svibanj'=>'Svibanj','Lipanj'=>'Lipanj','Srpanj'=>'Srpanj','Kolovoz'=>'Kolovoz',
                    'Rujan'=>'Rujan','Listopad'=>'Listopad','Studeni'=>'Studeni','Prosinac'=>'Prosinac',
                ]),

                SelectFilter::make('godina')->label('Godina')
                    ->options(function () {
                        $qb = Budget::query()->orderBy('godina');
                        if (! Auth::user()?->isAdmin()) {
                            $qb->where('user_id', Auth::id());
                        }
                        return $qb->pluck('godina', 'godina');
                    })
                    ->placeholder('Sve')
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;
                        if ($value === null || $value === '') {
                            return $query; // bez filtra
                        }
                        return $query->whereHas('budget', fn (Builder $b) => $b->where('godina', $value));
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
                    ->form(static::additionalFormFields())    // isti layout u modalu
                    ->mutateFormDataUsing(fn (array $data) => $data + ['user_id' => auth()->id()])
                    ->modalHeading('Novi trošak'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit'   => Pages\EditExpense::route('/{record}/edit'),
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