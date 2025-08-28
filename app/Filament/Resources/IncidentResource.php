<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncidentResource\Pages;
use App\Models\Incident;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\{TextInput, Select, DatePicker, FileUpload, Textarea};
use Filament\Tables\Columns\{TextColumn, ImageColumn, BadgeColumn};
use Filament\Tables\Filters\{SelectFilter, Filter};
use Illuminate\Support\Facades\Auth;
use App\Traits\AutoAssignsUser;


class IncidentResource extends Resource
{
    use AutoAssignsUser; // ⬅️ ako već koristiš ovaj trait
  

    protected static ?string $model = Incident::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationGroup = 'Moduli';
    protected static ?string $navigationLabel = 'Incidenti';
    protected static ?string $label = 'Incidenti';
    protected static ?int $navigationSort = 8;
    protected static ?string $pluralLabel = 'Incidenti';

    /** Forma – user_id automatski */
    public static function form(Form $form): Form
    {
        return static::assignUserField($form);
    }

    /** Ostatak polja forme */
    public static function additionalFormFields(): array
    {
        return [
        TextInput::make('location')->label('Lokacija')
            ->required(),

        Select::make('type_of_incident')->label('Vrsta Incidenta')
            ->options(['LTA' => 'LTA - Ozljeda na radu', 'MTA' => 'MTA - Pružanje PP izvan tvrtke', 'FAA' => 'FAA - Pužanje PP u tvrtki'])
            ->required(),

        Select::make('permanent_or_temporary')->label('Vrsta Zaposlenja')
            ->options(['Permanent' => 'Stalni', 'Temporary' => 'Privremeni'])
            ->required(),

        DatePicker::make('date_occurred')
    ->label('Datum nastanka')
    ->required()
    ->reactive(),

DatePicker::make('date_of_return')
    ->label('Datum povratka na posao')
    ->reactive()
    ->after('date_occurred')
    ->afterStateUpdated(function ($state, $context, $set, $get) {
        $start = $get('date_occurred');
        $end = $state;

        if ($start && $end) {
            $startDate = \Carbon\Carbon::parse($start);
            $endDate = \Carbon\Carbon::parse($end);

            // Isključujemo dan nezgode
            $daysLost = $startDate->diffInWeekdays($endDate) - 1;
            $set('working_days_lost', max($daysLost, 0));
        }
    }),

TextInput::make('working_days_lost')
    ->label('Izgubljeni radni dani')
    ->numeric(), // ili ukloni ako želiš ručni unos

        Textarea::make('causes_of_injury')->label('Uzrok ozljede')->rows(2),
        Textarea::make('accident_injury_type')->label('Tip ozljede')->rows(2),
        TextInput::make('injured_body_part')->label('Ozlijeđeni dio tijela'),
        
        FileUpload::make('image_path')->label('Slika')
            ->image()
            ->placeholder('Povucite i ispustite datoteke ili pretražite')
            ->directory('pdfs'),

        TextInput::make('other')->label('Napomena - Podaci o ozlijeđenom radniku'),

        FileUpload::make('investigation_report')
                ->label('Dodaj Prilog - Izvještaj o istrazi')
                ->directory('pdfs')
                ->placeholder('Povucite i ispustite datoteke ili pretražite')
                ->acceptedFileTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'application/zip',               // ZIP podrška
                'application/x-rar-compressed',  // RAR podrška
    ])
    ->maxSize(20480) // Max po datoteci: 20MB (safety margin)
    ->preserveFilenames()
    ->multiple()
    ->maxFiles(5)
    ->enableOpen()
    ->enableDownload()
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set) {
        $maxTotalMB = 50;
        $totalBytes = 0;

        if (is_array($state)) {
            foreach ($state as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $totalBytes += $file->getSize();
                }
            }
        }

        if ($totalBytes > $maxTotalMB * 1024 * 1024) {
            $set('pdf', []);
            \Filament\Notifications\Notification::make()
                ->title("Ukupna veličina datoteka ne smije biti veća od {$maxTotalMB} MB.")
                ->danger()
                ->persistent()
                ->send();
    }
    }),
    ];
            
}

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('location')->label('Lokacija'),
            TextColumn::make('type_of_incident')->alignCenter()->label('Vrsta incidenta'),
            TextColumn::make('date_occurred')->alignCenter()->date()->label('Datum nastanka'),
            TextColumn::make('working_days_lost')->alignCenter()->label('Izgubljeni radni dani'),
            TextColumn::make('injured_body_part')->alignCenter()->label('Ozlijeđeni dio tijela'),
            ImageColumn::make('image_path')->label('Slika')->circular(),
            TextColumn::make('other')->label('Napomena'),
            BadgeColumn::make('investigation_report')
    ->label('Izvještaji')
    ->icon(fn ($record) =>
        is_array($record->investigation_report) && count($record->investigation_report) > 0
            ? 'heroicon-o-paper-clip'
            : null
    )
    ->color(fn ($record) =>
        is_array($record->investigation_report) && count($record->investigation_report) > 0
            ? 'info'
            : 'gray'
    )
    ->tooltip(fn ($record) =>
        is_array($record->investigation_report) && count($record->investigation_report) > 0
            ? implode("\n", $record->investigation_report)
            : 'Nema izvještaja'
    )
    ->alignCenter()
    ->formatStateUsing(fn ($state, $record) =>
        is_array($record->investigation_report) ? (string) count($record->investigation_report) : '0'
    ),
        ])
        ->filters([

    // Filter po vrsti incidenta
    SelectFilter::make('prikaz')
        ->label('Vrsta incidenta')
        ->options([
            'LTA' => 'LTA',
            'MTA' => 'MTA',
            'FAA' => 'FAA',
            'deaktivirani' => 'Deaktivirani',
        ])
        ->placeholder('Svi aktivni')
        ->query(function (Builder $query, array $data): Builder {
            return match ($data['value'] ?? 'svi') {
                'LTA', 'MTA', 'FAA' => $query->withoutTrashed()->where('type_of_incident', $data['value']),
                'deaktivirani' => $query->onlyTrashed(),
                default => $query->withoutTrashed(),
            };
        }),
            // Filter po godini nastanka
    SelectFilter::make('godina_filter')
    ->label('Godina nastanka')
    ->options(function () {
        return ['' => 'Sve'] + \App\Models\Incident::query()
            ->selectRaw('YEAR(date_occurred) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year', 'year')
            ->toArray();
   })
    ->query(function (Builder $query, array $data) {
        if (!empty($data['value'])) {
            $query->whereYear('date_occurred', $data['value']);
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
            Tables\Actions\DeleteBulkAction::make()
        ->label('Deaktiviraj odabrane'),

    Tables\Actions\RestoreBulkAction::make()
        ->label('Vrati odabrane')
        ->visible(fn () => request('tableFilters.prikaz.value') === 'deaktivirani'),

    Tables\Actions\ForceDeleteBulkAction::make()
        ->label('Trajno izbriši odabrane')
        ->requiresConfirmation()
        ->visible(fn () => request('tableFilters.prikaz.value') === 'deaktivirani'),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncidents::route('/'),
            'create' => Pages\CreateIncident::route('/create'),
            'edit' => Pages\EditIncident::route('/{record}/edit'),
        ];
    }

 /** Scope po useru (osim admina) + bez globalnog soft-delete scopea */
    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
        return Auth::user()?->isAdmin() ? $q : $q->where('user_id', Auth::id());
    }

    /** Badge broji “samo moje” osim za admina */
    public static function getNavigationBadge(): ?string
    {
        $q = static::getModel()::query();
        if (!Auth::user()?->isAdmin()) {
            $q->where('user_id', Auth::id());
        }
        return (string) $q->count();
    }

    /** Global search scope */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }
}