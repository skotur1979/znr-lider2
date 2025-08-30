<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalProtectiveEquipmentLogResource\Pages;
use App\Filament\Resources\PersonalProtectiveEquipmentLogResource\RelationManagers;
use App\Filament\Resources\PersonalProtectiveEquipmentLogResource\RelationManagers\PersonalProtectiveEquipmentItemRelationManager;
use App\Filament\Resources\PersonalProtectiveEquipmentLogResource\RelationManagers\ItemsRelationManager;
use App\Models\PersonalProtectiveEquipmentLog;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\BadgeColumn;
use Carbon\Carbon;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Traits\AutoAssignsUser; // ✅ isti trait kao u drugim modulima

class PersonalProtectiveEquipmentLogResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = PersonalProtectiveEquipmentLog::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $label = 'OZO';
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $pluralLabel = 'Osobna zaštitna oprema';

    public static function getNavigationGroup(): ?string
    {
        return 'Moduli';
    }

    public static function getNavigationLabel(): string
    {
        return 'Upisnik OZO';
    }

    /** Admin vidi sve; ostali samo svoje */
    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with('items');

        if (Auth::user()?->isAdmin()) {
            return $q;
        }

        // scope samo ako kolona postoji (da nav ne padne tijekom migracije)
        if (Schema::hasColumn((new PersonalProtectiveEquipmentLog)->getTable(), 'user_id')) {
            $q->where('user_id', Auth::id());
        }

        return $q;
    }

    /** Forma: trait ubacuje hidden user_id + ova polja */
    public static function form(Form $form): Form
    {
        return static::assignUserField(
            $form->schema(static::additionalFormFields())
        );
    }

    public static function additionalFormFields(): array
    {
        return [
                Section::make('Podaci o zaposleniku')
                    ->schema([
                        TextInput::make('user_last_name')
                            ->label('Prezime i ime')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('user_oib')
                            ->label('OIB')
                            ->required()
                            ->maxLength(11),
                        TextInput::make('workplace')
                            ->label('Radno mjesto')
                            ->maxLength(255),
                        TextInput::make('organization_unit')
                            ->label('Organizacijska jedinica')
                            ->maxLength(255),
                    ])->columns(2),

       ];
}

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('user_last_name')->label('Ime i prezime')->searchable(),
            TextColumn::make('user_oib')->label('OIB')->alignCenter(),

            ViewColumn::make('nazivi')
                ->label('Naziv OZO')->alignCenter()
                ->view('filament.columns.ozo-nazivi'),

            ViewColumn::make('izdano')
                ->label('Izdano')
                ->view('filament.columns.ozo-izdano'),

            ViewColumn::make('items')
                ->label('Istek')
                ->view('filament.columns.ozo-items-expiring'),
        ])
        ->filters([
    SelectFilter::make('pregled')
    ->label('Prikaz')
    ->options([
        'svi' => 'Svi zaposlenici',
        'istek' => 'Samo OZO s istekom u 30 dana',
        'deaktivirani' => 'Deaktivirani',
    ])
    ->default('svi')
    ->placeholder('Odaberi...') // Ovo se prikazuje, ali neće biti prihvaćeno u query
    ->query(function (Builder $query, array $data): Builder {
        return match ($data['value'] ?? 'svi') {
            'istek' => $query
                ->withoutTrashed()
                ->whereHas('items', function ($subQuery) {
                    $subQuery->whereNotNull('end_date')
                             ->whereBetween('end_date', [now(), now()->addDays(30)]);
                }),
            'deaktivirani' => $query->onlyTrashed(),
            'svi' => $query->withoutTrashed(),
            default => $query->whereRaw('0=1'), // za svaki slučaj: ako je null ili nešto nepoznato – ne prikaži ništa
            };
        }),
])
        ->actions([
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->label('Deaktiviraj')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn ($record) => !$record->trashed()),

                Tables\Actions\RestoreAction::make()
                    ->label('Vrati')
                    ->icon('heroicon-o-refresh')
                    ->color('success')
                    ->visible(fn ($record) => $record->trashed()),

                Tables\Actions\ForceDeleteAction::make()
                    ->label('Trajno izbriši')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn ($record) => $record->trashed()),
            ]),
        ]);
}


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalProtectiveEquipmentLogs::route('/'),
            'create' => Pages\CreatePersonalProtectiveEquipmentLog::route('/create'),
            'edit' => Pages\EditPersonalProtectiveEquipmentLog::route('/{record}/edit'),
        ];
    }
    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $q = static::getModel()::query();
            if (! Auth::user()?->isAdmin()
                && Schema::hasColumn((new PersonalProtectiveEquipmentLog)->getTable(), 'user_id')) {
                $q->where('user_id', Auth::id());
            }
            return (string) $q->count();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }
}