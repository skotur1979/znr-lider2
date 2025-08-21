<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Builder;

class MedicalReferralsRelationManager extends RelationManager
{
    protected static string $relationship = 'medicalReferrals';
    protected static ?string $title = 'RA-1 Uputnice';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('date')->label('Datum')->required(),
            Forms\Components\Textarea::make('job_description')->label('Opis posla')->required()->rows(2),
            Forms\Components\Textarea::make('tools')->label('Alati')->rows(2),
            Forms\Components\Textarea::make('location_conditions')->label('Uvjeti na mjestu rada')->rows(2),
            Forms\Components\Textarea::make('organization')->label('Organizacija rada')->rows(2),
            Forms\Components\Textarea::make('activity')->label('Vrsta aktivnosti')->rows(2),
            Forms\Components\Textarea::make('hazards')->label('Opasnosti')->rows(2),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('date')->date('d.m.Y.')->label('Datum'),
            Tables\Columns\TextColumn::make('job_description')->label('Opis posla')->limit(30),
        ]);
    }
}
