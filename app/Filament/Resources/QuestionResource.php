<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Auth;
use App\Traits\AutoAssignsUser;

class QuestionResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Upravljanje';
    protected static ?string $navigationLabel = 'Pitanja';
    protected static ?string $pluralModelLabel = 'Pitanja';
    protected static ?string $modelLabel = 'Pitanje';
    protected static ?int $navigationSort = 31;

    public static function form(Form $form): Form
    {
        return static::assignUserField($form);
    }

    public static function additionalFormFields(): array
    {
        return [
            Select::make('test_id')
                ->label('Test')
                ->relationship('test', 'naziv', modifyQueryUsing: function (Builder $q) {
                    if (! Auth::user()?->isAdmin()) {
                        $q->where('user_id', Auth::id());
                    }
                })
                ->required(),
            TextInput::make('tekst')
                ->label('Tekst pitanja')
                ->required(),
                FileUpload::make('slika_path')
            ->label('Slika uz pitanje')
            ->image()
            ->directory('questions')
            ->maxSize(2048),

            Repeater::make('answers')
                ->label('Odgovori')
                ->relationship()
                ->schema([
                    TextInput::make('tekst')
                        ->label('Tekst odgovora')
                        ->required(),
                        FileUpload::make('slika_path')
                    ->label('Slika uz odgovor')
                    ->image()
                    ->directory('answers')
                    ->maxSize(2048),
                        
                    Toggle::make('is_correct')
                        ->label('Točan odgovor'),
                ])
                ->columns(2)
                ->createItemButtonLabel('Dodaj odgovor'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tekst')->label('Pitanje')->limit(50),
                BadgeColumn::make('answers_count')
                    ->label('Broj odgovora')
                    ->counts('answers'),
    ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
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
