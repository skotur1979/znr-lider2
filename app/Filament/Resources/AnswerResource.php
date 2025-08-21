<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnswerResource\Pages;
use App\Filament\Resources\AnswerResource\RelationManagers;
use App\Models\Answer;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;

class AnswerResource extends Resource
{
    protected static ?string $model = Answer::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Upravljanje';
    protected static ?int $navigationSort = 32;

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Select::make('question_id')
                ->label('Pitanje')
                ->relationship('question', 'tekst')
                ->required(),

            TextInput::make('tekst')
                ->label('Odgovor')
                ->required(),

            Toggle::make('is_correct')
                ->label('Točan odgovor'),
        ]);
    }

    public static function table(Table $table): Table
{
    return $table->columns([
        TextColumn::make('question.tekst')->label('Pitanje')->limit(50),
        TextColumn::make('tekst')->label('Odgovor'),
        IconColumn::make('is_correct')
    ->label('Točno?')
    ->boolean(), // ✅ ispravno
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
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnswers::route('/'),
            'create' => Pages\CreateAnswer::route('/create'),
            'edit' => Pages\EditAnswer::route('/{record}/edit'),
        ];
    }    
}
