<?php

namespace App\Filament\Resources\PersonalProtectiveEquipmentLogResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Resources\Form;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\View;

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

        TextInput::make('standard')->label('HRN EN'), // NOVO
        TextInput::make('size')->label('Veličina'),
        TextInput::make('duration_months')->label('Rok uporabe (mjeseci)'),
        DatePicker::make('issue_date')->label('Datum izdavanja'),
        DatePicker::make('end_date')->label('Datum isteka'),
        View::make('filament.components.signature-pad')->label('Unos potpisa'),

FileUpload::make('signature')
    ->label('Spremi potpisanu sliku ovdje')
    ->directory('signatures')
    ->visibility('public')
    ->image()
    ->maxSize(2048)
    ->columnSpanFull(),
        DatePicker::make('return_date')->label('Datum vraćanja'), // NOVO
    ]);
}


    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('equipment_name')->label('Naziv OZO'),
TextColumn::make('standard')->label('HRN EN'),
TextColumn::make('size')->label('Veličina'),
TextColumn::make('duration_months')->label('Rok (mjeseci)'),
TextColumn::make('issue_date')->label('Izdano')->date(),
TextColumn::make('end_date')->label('Istek')->date(),

\Filament\Tables\Columns\ImageColumn::make('signature')
    ->label('Potpis')
    ->disk('public')
    ->height(40)
    ->width(100),

TextColumn::make('return_date')->label('Datum vraćanja')->date(),
        ])
        ->headerActions([
            \Filament\Tables\Actions\CreateAction::make()->label('Dodaj OZO'),
        ])
        ->actions([
            \Filament\Tables\Actions\EditAction::make(),
            \Filament\Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            \Filament\Tables\Actions\DeleteBulkAction::make(),
        ]);
}

}