<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObservationResource\Pages;
use App\Models\Observation;
use App\Models\Employee;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Traits\AutoAssignsUser;
use Filament\Forms\Components\Hidden; // ⬅️ dodano

class ObservationResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = Observation::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static ?string $navigationGroup = 'Upravljanje';
    protected static ?string $navigationLabel = 'Zapažanja';
    protected static ?int $navigationSort = 4;
    protected static ?string $label = 'Zapažanje';
    protected static ?string $pluralLabel = 'Zapažanja';

    /** FORM – eksplicitno dodajemo user_id kao Hidden s default(Auth::id()) */
    public static function form(Form $form): Form
    {
        return $form->schema(array_merge([
            Hidden::make('user_id')->default(fn () => Auth::id()),
        ], static::additionalFormFields()));
    }

    /** ostatak forme ostaje identičan – samo polja bez $form->schema() */
    public static function additionalFormFields(): array
    {
        return [
            Forms\Components\DatePicker::make('incident_date')->label('Datum')->required(),
            Forms\Components\Select::make('observation_type')
                ->label('Vrsta zapažanja')
                ->options([
                    'Near Miss' => 'Near Miss - Skoro nezgoda',
                    'Negative Observation' => 'Negativno zapažanje',
                    'Positive Observation' => 'Pozitivno zapažanje',
                ])
                ->required(),
            Forms\Components\TextInput::make('location')->label('Lokacija')->required(),
            Forms\Components\TextInput::make('item')->label('Opis Zapažanja')->required(),
            Forms\Components\TextInput::make('potential_incident_type')
                ->label('Vrsta opasnosti')
                ->datalist([
                    'Kontakt s pokretnim dijelovima strojeva',
                    'Utapanje ili gušenje',
                    'Izloženost struji',
                    'Izloženost ekstremnim temperaturama',
                    'Izloženost vatri',
                    'Pad s visine',
                    'Pad na istoj razini',
                    'Udarac pokretnim vozilom',
                    'Udarac pokretnim, letećim ili padajućim predmetom',
                    'Udarac u nešto nepomično',
                    'Ručno rukovanje, podizanje ili nošenje',
                    'Profesionalna bolest/bolest',
                    'Fizički napad',
                    'Padovi, spoticanje ili pokliznuće',
                    'Incident s trećom stranom',
                    'Zarobljenost nečim što se ruši',
                    'Ostalo',
                    'Porezotine, ogrebotine ili abrazije',
                    'Blokirana protupožarna oprema',
                    'Blokirani evakuacijski putevi',
                    'Nedostatak odgovarajuće rasvjete',
                    'Nedostatak čistoće',
                    'Nepravilno skladištenje',
                ])
                ->required(),
            Forms\Components\FileUpload::make('picture_path')
                ->label('Slika')
                ->placeholder('Povucite i ispustite datoteke ili pretražite')
                ->image()
                ->directory('observations'),
            Forms\Components\Textarea::make('action')->label('Potrebna radnja'),
            Forms\Components\TextInput::make('responsible')
                ->label('Odgovorna osoba')
                ->datalist(
                    Employee::orderBy('name')
                        ->pluck('name')
                        ->unique()
                        ->toArray()
                )
                ->placeholder('Upiši ime'),
            Forms\Components\DatePicker::make('target_date')->label('Rok za provedbu'),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'Not started' => 'Nije započeto',
                    'In progress' => 'U tijeku',
                    'Complete' => 'Završeno',
                ])
                ->required(),
            Forms\Components\Textarea::make('comments')->label('Komentar'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('incident_date')->label('Datum')->wrap()->sortable()->alignCenter()->date('d-m-Y'),
                Tables\Columns\TextColumn::make('observation_type')
    ->wrap()
    ->alignCenter()
    ->label('Vrsta zapažanja')
    ->formatStateUsing(fn (string $state) => match ($state) {
        'Near Miss' => 'NM-Skoro nezgoda',
        'Negative Observation' => 'Negativno zapažanje',
        'Positive Observation' => 'Pozitivno zapažanje',
        default => $state,
    }),
                Tables\Columns\TextColumn::make('location')->alignCenter()->label('Lokacija'),
                Tables\Columns\TextColumn::make('item')->size('sm')->wrap()->label('Stavka'),
                Tables\Columns\TextColumn::make('potential_incident_type')->size('sm')->alignCenter()->wrap()->label('Vrsta opasnosti'),
                Tables\Columns\ImageColumn::make('picture_path')->label('Slika')->width(80),
                Tables\Columns\TextColumn::make('action')->size('sm')->wrap()->label('Potrebna radnja')->limit(25),
                Tables\Columns\TextColumn::make('responsible')->alignCenter()->label('Odgovorna osoba'),
                Tables\Columns\TextColumn::make('target_date')
                    ->label('Rok za provedbu')->alignCenter()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d-m-Y'))
                    ->color(function ($record) {
                        if (!$record->target_date || $record->status === 'Complete') {
                            return null;
                        }

                        $datum = \Carbon\Carbon::parse($record->target_date);
                        $danas = \Carbon\Carbon::today();

                        if ($datum->isPast()) {
                            return 'danger';
                        }

                        if ($datum->diffInDays($danas) <= 30) {
                            return 'warning';
                        }

                        return null;
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')->alignCenter()
                    ->colors([
                        'danger' => 'Not started',
                        'warning' => 'In progress',
                        'success' => 'Complete',
                    ])
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'Not started' => 'Nije započeto',
                        'In progress' => 'U tijeku',
                        'Complete' => 'Završeno',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('comments')->label('Komentar')->limit(20),
            ])
->filters([
    SelectFilter::make('observation_filter')
        ->label('Vrsta zapažanja')
        ->options([
            'near_miss' => 'Near Miss - Skoro nezgoda',
            'negative' => 'Negativno zapažanje',
            'positive' => 'Pozitivno zapažanje',
            'trashed' => 'Deaktivirani',
        ])
        ->query(function (Builder $query, array $data) {
            return match ($data['value']) {
                'near_miss' => $query->where('observation_type', 'Near Miss')->whereNull('deleted_at'),
                'negative' => $query->where('observation_type', 'Negative Observation')->whereNull('deleted_at'),
                'positive' => $query->where('observation_type', 'Positive Observation')->whereNull('deleted_at'),
                'trashed' => $query->onlyTrashed(),
                default => $query,
            };
        }),

    SelectFilter::make('godina_filter')
        ->label('Godina nastanka')
        ->options(function () {
            return ['' => 'Sve'] + Observation::query()
                ->selectRaw('YEAR(incident_date) as year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year', 'year')
                ->toArray();
        })
        ->query(function (Builder $query, array $data) {
            if (!empty($data['value'])) {
                $query->whereYear('incident_date', $data['value']);
            }
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
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ])
            ->defaultSort('incident_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObservations::route('/'),
            'create' => Pages\CreateObservation::route('/create'),
            'edit' => Pages\EditObservation::route('/{record}/edit'),
        ];
    }

     /** Admin vidi sve, korisnik samo svoje */
    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);

        return Auth::user()?->isAdmin()
            ? $q
            : $q->where('user_id', Auth::id());
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function getNavigationBadge(): ?string
    {
        $q = static::getModel()::query();
        if (! Auth::user()?->isAdmin()) {
            $q->where('user_id', Auth::id());
        }
        return (string) $q->count();
    }

    /** Fallback da se user_id sigurno upiše */
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        return $data;
    }
}