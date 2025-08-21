<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestResource\Pages;
use App\Filament\Resources\TestResource\RelationManagers;
use App\Models\Test;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;


class TestResource extends Resource
{
    protected static ?string $model = Test::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Upravljanje';
    protected static ?int $navigationSort = 30; // da bude ispod Budžeta

    

    public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('naziv')->required(),
        TextInput::make('sifra')->required()->unique(ignoreRecord: true),
        TextInput::make('minimalni_prolaz')->label('Minimalni prolaz (%)')->numeric()->default(75),
        Textarea::make('opis')->nullable(),
    ]);
}

public static function table(Table $table): Table
{
    return $table->columns([
        TextColumn::make('naziv')->searchable(),
        TextColumn::make('sifra'),
        TextColumn::make('minimalni_prolaz')->label('Prolaz (%)'),
        TextColumn::make('created_at')->label('Dodano')->date(),
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
            'index' => Pages\ListTests::route('/'),
            'create' => Pages\CreateTest::route('/create'),
            'edit' => Pages\EditTest::route('/{record}/edit'),
        ];
    }    
    public static function canAccess(): bool
{
    return auth()->user()?->isAdmin();
}
}
