<?php

namespace App\Filament\Resources\PersonalProtectiveEquipmentLogResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Resources\Form;
use Filament\Forms;
use Filament\Tables\Columns\{TextColumn, BadgeColumn, ImageColumn};
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\{EditAction, DeleteAction, Action};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Popis osobne zaštitne opreme';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('equipment_name')
                ->label('Naziv OZO')
                ->required()
                ->datalist([
                    'Zaštitna Kaciga',
                    'Zaštitne naočale prozirne',
                    'Zaštitne Rukavice',
                    'Reflektirajući prsluk',
                    'Zaštitne cipele s kapicom',
                    'Zaštitne gumene čizme',
                    'Radne hlače',
                    'Radna jakna',
                    'Majca s kratkim rukavima',
                    'Majca s dugim rukavima',
                    'Zimska jakna sa rukavima',
                    'Manžeta za zaštitu podlaktice',
                    'Zaštitna polumaska s filterima',
                ]),

            TextInput::make('standard')->label('HRN EN')->maxLength(64),
            TextInput::make('size')->label('Veličina')->maxLength(20),

            TextInput::make('duration_months')
                ->label('Rok uporabe (mjeseci)')
                ->numeric()->minValue(0)->maxValue(120)
                ->reactive()
                ->afterStateUpdated(fn ($state, $set, $get) => self::recalcEndDate($set, $get)),

            DatePicker::make('issue_date')
                ->label('Datum izdavanja')
                ->required()
                ->reactive()
                ->afterStateUpdated(fn ($state, $set, $get) => self::recalcEndDate($set, $get)),

            DatePicker::make('end_date')
                ->label('Datum isteka')
                ->disabled()
                ->dehydrated(false)
                ->helperText('Automatski izračun iz “Izdano” + “Rok (mjeseci)”.'),

            // potpis – vrijednost se piše u ovo hidden polje
            Hidden::make('signature')->reactive(),

            // signature-pad view, vezan na isto stanje preko statePath('signature')
            View::make('filament.components.ozo-signature')
                ->label('Potpis – preuzeo OZO')
                ->columnSpanFull()
                ->statePath('signature'),

            DatePicker::make('return_date')->label('Datum vraćanja'),
        ])->columns(4);
    }

    protected static function recalcEndDate(callable $set, callable $get): void
    {
        $issue  = $get('issue_date');
        $months = (int) $get('duration_months');
        if ($issue && $months > 0) {
            $set('end_date', Carbon::parse($issue)->addMonths($months)->format('Y-m-d'));
        } else {
            $set('end_date', null);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('equipment_name')->label('Naziv OZO')->searchable()->weight('semibold'),
                TextColumn::make('standard')->label('HRN EN')->toggleable(),
                TextColumn::make('size')->label('Veličina')->alignCenter(),
                TextColumn::make('duration_months')->label('Rok (mjeseci)')->alignCenter(),
                TextColumn::make('issue_date')->label('Izdano')->date('d.m.Y.')->alignCenter(),

                BadgeColumn::make('end_date')
                    ->label('Istek')
                    ->formatStateUsing(function ($state) {
                        if (blank($state)) return '—';
                        $dt = $state instanceof Carbon ? $state : Carbon::parse($state);
                        return $dt->format('d.m.Y.');
                    })
                    ->colors([
                        'success' => fn ($state) => $state && Carbon::parse($state)->gt(today()->addDays(30)),
                        'warning' => fn ($state) => $state && Carbon::parse($state)->between(today(), today()->addDays(30)),
                        'danger'  => fn ($state) => $state && Carbon::parse($state)->lt(today()),
                    ])
                    ->icon('heroicon-o-clock')
                    ->alignCenter(),

                TextColumn::make('return_date')->label('Datum vraćanja')->date('d.m.Y.')->alignCenter()->toggleable(),

                ImageColumn::make('signature')
                    ->label('Potpis')
                    ->disk('public')
                    ->height(40)
                    ->width(100)
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('isteklo')->label('Isteklo')
                    ->query(fn (Builder $q) => $q->whereNotNull('end_date')->where('end_date', '<', today())),
                Filter::make('uskoro')->label('Uskoro ističe (≤30d)')
                    ->query(fn (Builder $q) => $q->whereBetween('end_date', [today(), today()->addDays(30)])),
                Filter::make('vraceno')->label('Vraćeno')
                    ->query(fn (Builder $q) => $q->whereNotNull('return_date')),
            ])
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make()->label('Dodaj OZO'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('extend3')->label('Produži +3 mj')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->duration_months = max(0, (int) $record->duration_months) + 3;
                        $record->save();
                    }),
                Action::make('returnedToday')->label('Označi vraćeno danas')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['return_date' => today()])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

