<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\FileUpload;
use Saade\FilamentLaravelExport\Concerns\CanExport;
use App\Filament\Resources\EmployeeResource;
use App\Traits\AutoAssignsUser;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Panel;
use Filament\Facades\Filament;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;

class EmployeeResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Zaposlenici';

    protected static ?string $modelLabel = 'Zaposlenik';

    protected static ?string $pluralModelLabel = 'Zaposlenici';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Moduli';

    public static function form(Form $form): Form
{
    return $form->schema(static::additionalFormFields());
}

    public static function additionalFormFields(): array
    {
        return [
            Section::make('Osobni podatci')->schema([

                TextInput::make('name')->label('Prezime i ime (obavezno)')->prefixIcon('heroicon-o-user')->string()->filled()->required(),
                // ➕ NOVO – polja koja želiš imati i na uputnici
                TextInput::make('job_title')->label('Zanimanje')->prefixIcon('heroicon-o-briefcase')->nullable(),
                TextInput::make('education')->label('Školska sprema')->prefixIcon('heroicon-o-academic-cap')->nullable(),
                TextInput::make('place_of_birth')->label('Datum i mjesto rođenja')->prefixIcon('heroicon-o-academic-cap')->nullable(),
                TextInput::make('name_of_parents')->label('Ime oca – majke')->prefixIcon('heroicon-o-user-group')->nullable(),
                TextInput::make('address')->label('Adresa')->prefixIcon('heroicon-o-home')->nullable(),
                TextInput::make('gender')->label('Spol')->prefixIcon('heroicon-o-identification')->nullable(),
                TextInput::make('OIB')->label('OIB - osobni identifikacijski broj')->prefixIcon('heroicon-o-document')->nullable(),
                TextInput::make('phone')->label('Telefon/Mobitel')->prefixIcon('heroicon-o-phone')->nullable(),
                TextInput::make('email')->label('Email')->prefixIcon('heroicon-o-at-symbol')->nullable(),
                TextInput::make('workplace')->label('Radno mjesto')->prefixIcon('heroicon-o-briefcase')->nullable(),
                TextInput::make('organization_unit')->label('Organizacijska jedinica')->prefixIcon('heroicon-o-office-building')->nullable(),
                TextInput::make('contract_type')->label('Vrsta Ugovora')->prefixIcon('heroicon-o-office-building')->nullable(),
                DatePicker::make('employeed_at')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Datum zaposlenja (obavezno)')->filled(),
                DatePicker::make('contract_ended_at')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Datum prekida ugovora')->nullable(),

            ])->columns(3),

            Section::make('Liječnički pregled')->schema([
                DatePicker::make('medical_examination_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi od')->nullable(),
                DatePicker::make('medical_examination_valid_until')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi do')->nullable(),
                Textarea::make('article')->rows(1)->label('Članak 3. točke')->nullable(),
            ])->columns(3),

            /*Section::make('Napomena')->schema([
                Textarea::make('remark')->columnSpanFull()->rows(3)->nullable(),
            ])->columns(2),*/

            Section::make('Zaštita na radu - Rad na siguran način')->schema([
                DatePicker::make('occupational_safety_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi od')->nullable(),
            ])->columns(2),

            Section::make('Zaštita od požara - ZOP')->schema([

                DatePicker::make('fire_protection_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('ZOP - Vrijedi od')->nullable(),
                DatePicker::make('fire_protection_statement_at')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('ZOP Izjava od')->nullable(),
                DatePicker::make('evacuation_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Voditelj evakuacije vrijedi od')->nullable(),
            

            ])->columns(3),
                
            Section::make('Prva pomoć')->schema([
                DatePicker::make('first_aid_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi od')->nullable(),
                DatePicker::make('first_aid_valid_until')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi do')->nullable(),
            ])->columns(2),

            Section::make('Toksikologija - Rad s opasnim kemikalijama')->schema([
                DatePicker::make('toxicology_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi od')->nullable(),
                DatePicker::make('toxicology_valid_until')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi do')->nullable()
            ])->columns(2),

            /*Section::make('Rukovanje sa zapaljivim tekućinama i plinovima')->schema([
                DatePicker::make('handling_flammable_materials_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi od')->nullable(),
                DatePicker::make('handling_flammable_materials_valid_until')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi do')->nullable()
            ])->columns(2),*/

            Section::make('Ovlaštenik poslodavca za ZNR')->schema([
                DatePicker::make('employers_authorization_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi od')->nullable(),
                DatePicker::make('employers_authorization_valid_until')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi do')->nullable(),
            ])->columns(2),
            Section::make('Ostale edukacije i ovlaštenja')->schema([
            Repeater::make('certificates')
                ->label('Popis edukacija / ovlaštenja')
                ->relationship()
                ->createItemButtonLabel('Dodaj novi zapis')
                ->schema([
                    TextInput::make('title')
                        ->label('Naziv')
                        ->required(),
                    DatePicker::make('valid_from')
                        ->label('Vrijedi od')
                        ->required()
                        ->format('Y-m-d')
                        ->displayFormat('d.m.Y.')
                        ->timezone('Europe/Zagreb'),
                    DatePicker::make('valid_until')
                        ->label('Vrijedi do')
                        ->nullable()
                        ->format('Y-m-d')
                        ->displayFormat('d.m.Y.')
                        ->timezone('Europe/Zagreb'),
                ])
            ->columns(3)
            ->extraAttributes(['class' => 'p-2 border border-gray-700 rounded-md']) // ⬅️ manji padding
            ->collapsible()
            ->itemLabel(fn ($state) => $state['title'] ?? 'Nova stavka'),
        ]),
        FileUpload::make('pdf')
            ->label('Dodaj Prilog (max. 10)')
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
            ->maxFiles(10)
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

                TextColumn::make('name')->searchable()->sortable()->weight('bold')->label('Prezime i ime'),
                TextColumn::make('workplace')->sortable()->searchable()->wrap()->label('Radno mjesto')->size('sm'),


                BadgeColumn::make('medical_examination_valid_until')
                ->date('d.m.Y.')
                ->label('Liječnički (do)')->alignCenter()
                ->colors([
                    'primary'   => static fn ($date):bool => $date == null,
                    'success'   => static fn ($date):bool => $date && $date->diffInDays(Carbon::today()) > 30,
                    'warning'   => static fn ($date):bool => $date && $date->diffInDays(Carbon::today()) <= 30,
                    'danger'    => static fn ($date):bool => $date && $date->lt(Carbon::today())
                ])->sortable(),

                TextColumn::make('article')->sortable()->alignCenter()->searchable()->wrap()->label('Članak 3. točke')->size('sm'),
                /*TextColumn::make('remark')->sortable()->searchable()->label('Napomena')->size('sm'),*/

                TextColumn::make('occupational_safety_valid_from')
                ->label('ZNR (od)')
                ->date('d.m.Y.')
                ->sortable()->alignCenter(),

                /* BadgeColumn::make('occupational_safety_valid_from')
                ->date('d.m.Y.')
                ->label('ZNR (do)')
                ->colors([
                    'success'   => static fn ($date):bool => $date->diffInDays(Carbon::today()) > 30,
                    'warning'   => static fn ($date):bool => $date->diffInDays(Carbon::today()) <= 30,
                    'danger'    => static fn ($date):bool => $date->lt(Carbon::today())
                ])->sortable(), */

                BadgeColumn::make('toxicology_valid_until')
                ->date('d.m.Y.')
                ->label('Toksikologija (do)')->alignCenter()
                ->colors([
                    'success'   => static fn ($date):bool => $date !== null && $date->diffInDays(Carbon::today()) > 30,
                    'warning'   => static fn ($date):bool => $date !== null && $date->diffInDays(Carbon::today()) <= 30,
                    'danger'    => static fn ($date):bool => $date !== null && $date->lt(Carbon::today())
                ])
                ->sortable(),

/*                 TextColumn::make('first_aid_valid_from')
                ->date('d.m.Y.')
                ->label('Prva pomoć(od)')
                ->sortable()
                ->size('sm'), */

                BadgeColumn::make('employers_authorization_valid_until')
                ->date('d.m.Y.')
                ->label('Ovlaštenik ZNR (do)')->alignCenter()
                ->colors([
                    'success'   => static fn ($date):bool => $date !== null && $date->diffInDays(Carbon::today()) > 30,
                    'warning'   => static fn ($date):bool => $date !== null && $date->diffInDays(Carbon::today()) <= 30,
                    'danger'    => static fn ($date):bool => $date !== null && $date->lt(Carbon::today())
                ])
                ->sortable(),
                ViewColumn::make('certificates_filtered')
    ->label('Ostale edukacije')
    ->view('filament.components.certificates-filtered'),
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
                Filter::make('medical_examination_expired')->label('Liječnički (istekao)')->query(fn (Builder $query): Builder => $query->where('medical_examination_valid_until', '<', Carbon::today())),
                Filter::make('medical_examination_expiring')->label('Liječnički (uskoro ističe)')->query(fn (Builder $query): Builder => $query->where('medical_examination_valid_until', '>=', Carbon::today())->where('medical_examination_valid_until','<', Carbon::today()->addMonth())),
                Filter::make('toxicology_expired')->label('Toksikologija (istekla)')->query(fn (Builder $query): Builder => $query->where('toxicology_valid_until', '<', Carbon::today())),
                Filter::make('toxicology_expiring')->label('Toksikologija (uskoro ističe)')->query(fn (Builder $query): Builder => $query->where('toxicology_valid_until', '>=', Carbon::today())->where('toxicology_valid_until','<', Carbon::today()->addMonth())),
                

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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->where('user_id', auth()->id())
        ->with('certificates')
        ->withoutGlobalScopes([SoftDeletingScope::class]);
}

    public function isTableSearchable(): bool
    {
        return true;
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    

}
