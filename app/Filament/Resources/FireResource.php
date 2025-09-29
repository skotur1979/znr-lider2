<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FireResource\Pages;
use App\Filament\Resources\FireResource\RelationManagers;
use App\Models\Fire;
use Filament\Forms;
use Filament\Tables\Filters\Filter;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Auth; // ⬅️ dodaj na vrh
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\FileUpload;
use App\Traits\AutoAssignsUser;

class FireResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = Fire::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationLabel = 'Vatrogasni aparati';

    protected static ?string $modelLabel = 'Vatrogasni aparat';

    protected static ?string $pluralModelLabel = 'Vatrogasni aparati';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Ispitivanja';

    public static function form(Form $form): Form
{
    // AutoAssignsUser dodaje user_id; ostatak sheme ispod:
        return static::assignUserField($form)
            ->schema(static::additionalFormFields());
}
            public static function additionalFormFields(): array
{
    return [
                Section::make('Podatci o vatrogasnom aparatu')->schema([
                    TextInput::make('place')->label('Mjesto gdje se aparat nalazi (obavezno)')->prefixIcon('heroicon-o-home')->string()->filled(),
                    TextInput::make('type')->label('Tip aparata')->prefixIcon('heroicon-o-book-open')->nullable(),
                    // ⬇️ koristimo ALIAS ključ (model mappa na kolonu s kosom crtom)
                TextInput::make('factory_number_year_of_production')
                    ->label('Tvornički broj/Godina proizvodnje')
                    ->prefixIcon('heroicon-o-document')
                    ->nullable(),
                    TextInput::make('serial_label_number')->label('Serijski broj evidencijske naljepnice')->prefixIcon('heroicon-o-document-duplicate')->nullable(),
                ])->columns(2),

                Section::make('Ispitivanje vatrogasnog aparata')->schema([
                    DatePicker::make('examination_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Datum periodičkog servisa (obavezno)')->filled(),
                    DatePicker::make('examination_valid_until')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi do (obavezno)')->filled(),
                    TextInput::make('service')->label('Naziv servisera koji je servisirao aparat')->prefixIcon('heroicon-o-document-duplicate')->nullable(),
                    DatePicker::make('regular_examination_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Datum redovnog pregleda (obavezno)')->filled(),
                ])->columns(2),

                Section::make('Ostalo')->schema([
                    TextInput::make('visible')->label('Uočljivost i dostupnost aparata')->nullable(),
                    TextInput::make('remark')->label('Uočeni nedostatci')->nullable(),
                    TextInput::make('action')->label('Postupci otklanjanja')->nullable(),
                ])->columns(2),
                FileUpload::make('pdf')
                ->label('Dodaj Prilog (max. 5)')
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

                TextColumn::make('place')->searchable()->sortable()->weight('bold')->label('Mjesto gdje se aparat nalazi'),
                TextColumn::make('type')->alignCenter()->searchable()->sortable()->size('sm')->label('Tip aparata'),
                // ⬇️ PRIKAZ PREKO ALIASA (bez direktnog pristupa koloni s kosom crtom)
                // (Bez searchable/sortable jer v2 nema jednostavan callback za custom kolonu)
                TextColumn::make('factory_number_year_of_production')->alignCenter()->searchable()->sortable()->size('sm')->label('Tvor.broj/Godina proizv.'),
                TextColumn::make('examination_valid_from')->alignCenter()->date('d.m.Y.')->sortable()->label('Datum ispitivanja'),

                BadgeColumn::make('examination_valid_until')
                ->date('d.m.Y.')->alignCenter()
                ->label('Ispitivanje vrijedi do')
                ->colors([
                    'success'   => static fn ($date):bool => $date->diffInDays(Carbon::today()) > 30,
                    'warning'   => static fn ($date):bool => $date->diffInDays(Carbon::today()) <= 30,
                    'danger'    => static fn ($date):bool => $date->lt(Carbon::today())
                ])->sortable(),
                TextColumn::make('regular_examination_valid_from')->alignCenter()->date('d.m.Y.')->sortable()->label('Datum redovnog pregleda'),
                BadgeColumn::make('pdf')
    ->label('Prilozi')
    ->icon(fn ($record) =>
        is_array($record->pdf) && count($record->pdf) > 0
            ? 'heroicon-o-paper-clip'
            : null
    )
    ->color(fn ($record) =>
        is_array($record->pdf) && count($record->pdf) > 0
            ? 'info'
            : 'gray'
    )
    ->tooltip(fn ($record) =>
        is_array($record->pdf) && count($record->pdf) > 0
            ? implode("\n", $record->pdf)
            : 'Nema priloga'
    )
    ->alignCenter()
    ->formatStateUsing(fn ($state, $record) =>
        is_array($record->pdf) ? (string) count($record->pdf) : '0'
    ),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Filter::make('examination_validity_expired')->label('Ispitivanje (isteklo)')->query(fn (Builder $query): Builder => $query->where('examination_valid_until', '<', Carbon::today())),
                Filter::make('examination_validity_expiring')->label('Ispitivanje (uskoro ističe)')->query(fn (Builder $query): Builder => $query->where('examination_valid_until', '>', Carbon::today())->where('examination_valid_until','<', Carbon::today()->addMonth())),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->requiresConfirmation(),
                    Tables\Actions\RestoreAction::make()->requiresConfirmation(),
                    Tables\Actions\ForceDeleteAction::make()->requiresConfirmation()
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListFires::route('/'),
            'create' => Pages\CreateFire::route('/create'),
            'view' => Pages\ViewFire::route('/{record}'),
            'edit' => Pages\EditFire::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery()
        ->withoutGlobalScopes([SoftDeletingScope::class]);

    // Admin vidi sve
    if (Auth::user()?->isAdmin()) {
        return $query;
    }

    // Ostali vide samo svoje
    return $query->where('user_id', Auth::id());
}

    public function isTableSearchable(): bool
    {
        return true;
    }
    // (opcionalno) globalni search ograniči isto
public static function getGlobalSearchEloquentQuery(): Builder
{
    return static::getEloquentQuery();
}

// (opcionalno) badge neka broji filtrirano
public static function getNavigationBadge(): ?string
{
    $q = static::getModel()::query();
    if (! Auth::user()?->isAdmin()) {
        $q->where('user_id', Auth::id());
    }
    return (string) $q->count();
}
}