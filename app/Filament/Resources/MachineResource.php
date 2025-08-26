<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MachineResource\Pages;
use App\Filament\Resources\MachineResource\RelationManagers;
use App\Models\Machine;
use Filament\Forms;
use Filament\Tables\Filters\Filter;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
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
use App\Imports\MachinesImport;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;


class MachineResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = Machine::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationLabel = 'Radna Oprema';

    protected static ?string $modelLabel = 'Radna Oprema';

    protected static ?string $pluralModelLabel = 'Radna Oprema';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Moduli';

    public static function form(Form $form): Form
{
    return static::assignUserField($form);
}
           public static function additionalFormFields(): array
{
    return [
                Section::make('Podatci o radnoj opremi')->schema([
                    TextInput::make('name')->label('Naziv (obavezno)')->prefixIcon('heroicon-o-cog')->string()->filled(),
                    TextInput::make('manufacturer')->label('Proizvođač')->prefixIcon('heroicon-o-cog')->nullable(),
                    TextInput::make('factory_number')->label('Tvornički broj')->prefixIcon('heroicon-o-document')->nullable(),
                    TextInput::make('inventory_number')->label('Inventarni broj')->prefixIcon('heroicon-o-document-duplicate')->nullable()
                ])->columns(2),

                Section::make('Ispitivanje')->schema([
                    DatePicker::make('examination_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi od (obavezno)')->filled(),
                    DatePicker::make('examination_valid_until')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi do (obavezno)')->filled(),
                    TextInput::make('examined_by')
        ->label('Ispitao')
        ->placeholder('Naziv tvrtke')
        ->columnSpan(1),

    TextInput::make('report_number')
        ->label('Broj izvještaja')
        ->placeholder('Unesite broj izvještaja')
        ->columnSpan(1),
                ])->columns(2),

                Section::make('Ostalo')->schema([
                    TextInput::make('location')->label('Lokacija (obavezno)')->filled(),
                    Textarea::make('remark')->label('Napomena')->columnSpanFull()->rows(3)->nullable(),
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

                TextColumn::make('name')->searchable()->sortable()->weight('bold')->label('Naziv'),
                TextColumn::make('manufacturer')->searchable()->sortable()->size('sm')->label('Proizvođač'),
                TextColumn::make('factory_number')->alignCenter()->searchable()->sortable()->size('sm')->label('Tvor.broj'),
                TextColumn::make('examination_valid_from')->alignCenter()->date('d.m.Y.')->sortable()->label('Datum ispitivanja'),

                BadgeColumn::make('examination_valid_until')
                ->date('d.m.Y.')
                ->label('Ispitivanje vrijedi do')->alignCenter()
                ->colors([
                    'success'   => static fn ($date):bool => $date->diffInDays(Carbon::today()) > 30,
                    'warning'   => static fn ($date):bool => $date->diffInDays(Carbon::today()) <= 30,
                    'danger'    => static fn ($date):bool => $date->lt(Carbon::today())
                ])->sortable(),
                TextColumn::make('location')->label('Lokacija')->sortable(),
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
            'index' => Pages\ListMachines::route('/'),
            'create' => Pages\CreateMachine::route('/create'),
            'view' => Pages\ViewMachine::route('/{record}'),
            'edit' => Pages\EditMachine::route('/{record}/edit'),
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

    public static function getNavigationBadge(): ?string
{
    $q = static::getModel()::query();
    if (! Auth::user()?->isAdmin()) {
        $q->where('user_id', Auth::id());
    }
    return (string) $q->count();
}
public static function getGlobalSearchEloquentQuery(): Builder
{
    return static::getEloquentQuery();
}
}
