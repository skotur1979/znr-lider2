<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationLabel = 'Korisnici';
    protected static ?string $pluralModelLabel = 'Korisnici';
    protected static ?string $modelLabel = 'Korisnici';

    // Vidljivost u navigaciji (ostaje isto)
public static function shouldRegisterNavigation(): bool
{
    return auth()->user()?->isAdmin() === true;
}

/* Dozvole za Filament v2 – bez $user argumenta */
public static function canViewAny(): bool
{
    return auth()->user()?->isAdmin() === true;
}

public static function canCreate(): bool
{
    return auth()->user()?->isAdmin() === true;
}

public static function canEdit($record): bool
{
    return auth()->user()?->isAdmin() === true;
}

public static function canDelete($record): bool
{
    return auth()->user()?->isAdmin() === true;
}

public static function canDeleteAny(): bool
{
    return auth()->user()?->isAdmin() === true;
}


    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Ime')
                ->required(),

            TextInput::make('email')
                ->label('Email')
                ->required()
                ->email()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->label('Lozinka')
                ->password()
                ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                ->maxLength(255)
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn ($state) => filled($state)),

            Select::make('is_admin')
                ->label('Uloga')
                ->options([
                    1 => 'Admin',
                    0 => 'Korisnik',
                ])
                ->default(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Ime i Prezime')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                BadgeColumn::make('is_admin')
                    ->label('Uloga')
                    ->enum([1 => 'Admin', 0 => 'Korisnik'])
                    ->colors(['success' => 1, 'danger' => 0]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_admin')
                    ->label('Uloga')
                    ->options([1 => 'Admin', 0 => 'Korisnik']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return auth()->user()?->isAdmin() ? (string) static::getModel()::count() : null;
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        return $data;
    }

    public static function mutateFormDataBeforeUpdate(array $data): array
    {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        return $data;
    }
}
