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
use Illuminate\Support\Facades\Auth;
use App\Traits\AutoAssignsUser;

class AnswerResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = Answer::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Testiranje';
    protected static ?string $navigationLabel = 'Odgovori';
    protected static ?string $pluralModelLabel = 'Odgovori';
    protected static ?string $modelLabel = 'Odgovor';
    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return static::assignUserField($form);
    }

    public static function additionalFormFields(): array
    {
        return [
            Select::make('question_id')
                ->label('Pitanje')
                ->relationship('question', 'tekst')
                ->required(),

            TextInput::make('tekst')
                ->label('Odgovor')
                ->required(),

            Toggle::make('is_correct')
                ->label('Točan odgovor'),
        ];
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
    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery();
        if (! Auth::user()?->isAdmin()) {
            $q->where('user_id', Auth::id());
        }
        return $q;
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
