<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalReferralResource\Pages;
use App\Models\MedicalReferral;
use App\Models\Employee;
use Closure;
use Filament\Forms\Components\{
    TextInput, Textarea, DatePicker, Select, CheckboxList, Section, Grid, Checkbox, Group, Toggle
};
use Filament\Resources\{Form, Resource, Table};
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use App\Traits\AutoAssignsUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MedicalReferralResource extends Resource
{
    use AutoAssignsUser;          // ← DODANO
    protected static ?string $model = MedicalReferral::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Zaposlenici';
    protected static ?string $navigationLabel  = 'RA-1 Uputnice';
    protected static ?string $pluralModelLabel = 'RA-1 Uputnice';
    protected static ?string $modelLabel       = 'RA-1 Uputnica';
    protected static ?int $navigationSort = 2; // redoslijed unutar grupe

    private static function isAdminUser($user): bool
{
    if (! $user) return false;

    // 0) Super fallback: korisnik #1 = admin
    if ((int) $user->id === 1) return true;

    // 1) Spatie roles: ako postoje
    try {
        if (method_exists($user, 'getRoleNames')) {
            $roles = $user->getRoleNames()->toArray(); // Collection -> array
            foreach ($roles as $r) {
                $name = trim((string) $r);
                // hvataj sve “admin” varijante i sinonime
                if (
                    Str::contains(Str::lower($name), 'admin') ||
                    in_array(Str::lower($name), ['administrator', 'super-admin', 'super admin', 'owner', 'root'])
                ) {
                    return true;
                }
            }
        }
        if (method_exists($user, 'hasAnyRole')) {
            if ($user->hasAnyRole([
                'admin', 'Admin', 'administrator', 'Administrator',
                'super-admin', 'Super Admin', 'owner', 'Owner', 'root', 'Root',
            ])) {
                return true;
            }
        }
        if (method_exists($user, 'hasRole')) {
            foreach (['admin','Admin','administrator','Administrator','super-admin','Super Admin','owner','Owner','root','Root'] as $r) {
                if ($user->hasRole($r)) return true;
            }
        }
    } catch (\Throwable $e) {
        // ignore
    }

    // 2) Flag na modelu (ako postoji)
    if (isset($user->is_admin) && (bool) $user->is_admin) {
        return true;
    }

    // 3) Policy / permissions — ako imaš definirano u AuthServiceProvideru/Policy-ju
    try {
        if (method_exists($user, 'can') && $user->can('viewAny', \App\Models\MedicalReferral::class)) {
            return true;
        }
    } catch (\Throwable $e) {
        // ignore
    }

    return false;
}
    /** Lista / forma */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Povezivanje sa zaposlenikom')
                ->schema([
                    Toggle::make('manual_entry')
                        ->label('Novi radnik (još nije u bazi)')
                        ->helperText('Ako uključiš, podatke upiši ručno')
                        ->reactive(),

                    Select::make('employee_id')
    ->label('Zaposlenik')
    ->options(Employee::query()->pluck('name', 'id'))
    ->searchable()
    ->reactive()
    ->required(fn ($get) => ! $get('manual_entry'))   // ← bez tipa
    ->hidden(fn ($get) => $get('manual_entry'))       // ← bez tipa
    ->afterStateUpdated(function ($state, callable $set, $get) {  // ← bez tipa
        if ($get('manual_entry')) return;

        $emp = Employee::find($state);
        $set('full_name',       $emp->name            ?? '');
        $set('oib',             $emp->OIB             ?? '');
        $set('job_title',       $emp->job_title       ?? '');
        $set('education',       $emp->education       ?? '');
        $set('name_of_parents', $emp->name_of_parents ?? '');
        $set('place_of_birth',  $emp->place_of_birth  ?? '');
    }),
                ]),

            Section::make('Podaci o zaposleniku')
                ->schema([
                    TextInput::make('referral_number')->label('Broj'),
                    DatePicker::make('referral_date')->label('Datum'),
                    TextInput::make('employer_name')->label('Naziv poslodavca'),
                    TextInput::make('employer_address')->label('Adresa poslodavca'),
                    TextInput::make('employer_oib')->label('OIB poslodavca'),

                    TextInput::make('full_name')
    ->label('Ime i prezime')
    ->required(fn ($get) => $get('manual_entry'))      // ← bez tipa
    ->disabled(fn ($get) => ! $get('manual_entry'))    // ← bez tipa
    ->afterStateHydrated(function (callable $set, $state, $record) { // samo 3 parametra
        if ($record?->employee && !($record->manual_entry ?? false)) {
            $set('full_name', $record->employee->name ?? '');
        }
    }),

                    TextInput::make('name_of_parents')
    ->label('Ime oca – majke')
    ->disabled(fn ($get) => ! $get('manual_entry'))    // ← bez tipa
    ->afterStateHydrated(function (callable $set, $state, $record) {
        if ($record?->employee && !($record->manual_entry ?? false)) {
            $set('name_of_parents', $record->employee->name_of_parents ?? '');
        }
    }),
                    TextInput::make('place_of_birth')
    ->label('Datum i mjesto rođenja')
    ->disabled(fn ($get) => ! $get('manual_entry'))    // ← bez tipa
    ->afterStateHydrated(function (callable $set, $state, $record) {
        if ($record?->employee && !($record->manual_entry ?? false)) {
            $set('place_of_birth', $record->employee->place_of_birth ?? '');
        }
    }),

                    TextInput::make('oib')
                        ->label('OIB')
                        ->required(fn (Closure $get) => $get('manual_entry'))
                        ->numeric()
                        ->disabled(fn (Closure $get) => ! $get('manual_entry'))
                        ->afterStateHydrated(function (callable $set, $state, $record) {
                            if ($record?->employee) {
                                $set('oib', $record->employee->OIB);
                            }
                        }),

                    TextInput::make('job_title')
                        ->label('Zanimanje')
                        ->disabled(fn (Closure $get) => ! $get('manual_entry')),

                    TextInput::make('education')
                        ->label('Školska sprema')
                        ->disabled(fn (Closure $get) => ! $get('manual_entry')),
                ])->columns(2),

            Section::make('Opis poslova i uvjeti')
                ->schema([
                    TextInput::make('health_jobs_description')
                        ->label('Poslovi za koje se utvrđuje zdravstvena sposobnost')
                        ->maxLength(80)->extraAttributes(['maxlength' => 80])->rule('max:80')
                        ->reactive()
                        ->helperText(fn ($get) => mb_strlen((string) $get('health_jobs_description')) . '/80'),
                    TextInput::make('law_reference')->label('Poslovi su prema članku'),
                    TextInput::make('law_reference1')->label('točka Pravilnika o poslovima s posebnimu uvijetima rada'),
                    TextInput::make('special_conditions')
                        ->label('Poslovi prema drugim zakonima, propisima ili kolektivom')
                        ->maxLength(110)->extraAttributes(['maxlength' => 110])->rule('max:110')
                        ->reactive()
                        ->helperText(fn ($get) => mb_strlen((string) $get('special_conditions')) . '/110'),
                ])->columns(2),

            Section::make('Radni staž')
                ->schema([
                    TextInput::make('total_years')->label('Ukupni radni staž'),
                    TextInput::make('work_years_in_job')->label('Radni staž na poslovima za koje se utvrđuje zdravstvena sposobnost'),
                ])->columns(2),

            Section::make('Zdravstveni pregled')
                ->schema([
                    Grid::make(1)->schema([
                        CheckboxList::make('exam_type')
                            ->label('Vrsta pregleda')
                            ->options([
                                'prethodni'  => 'Prethodni',
                                'periodični' => 'Periodički',
                                'izvanredni' => 'Izvanredni',
                            ])
                            ->columns(3),
                    ]),
                    DatePicker::make('last_exam_date')->label('Posljednji zdravstveni pregled je učinjen'),
                    TextInput::make('last_exam_reference')->label('Prema članku'),
                    TextInput::make('last_exam_reference1')->label('točki Pravilnika o poslovima s posebnim uvjetima rada'),
                    TextInput::make('last_exam_reference2')
                        ->label('ili')
                        ->extraAttributes(['class' => 'text-sm'])
                        ->maxLength(170)->extraAttributes(['maxlength' => 170])->rule('max:170')
                        ->reactive()
                        ->helperText(function ($get) {
                            $count = mb_strlen((string) $get('last_exam_reference2'));
                            return new HtmlString(
                                '<div class="text-xs space-y-1">'
                              .   '<div>(navesti zakon, propis ili kolektivni ugovor iz članka 2. stavka 1. podstavka 2. ili 3. Pravilnika)</div>'
                              .   '<div><strong>' . $count . '/170</strong></div>'
                              . '</div>'
                            );
                        }),
                    TextInput::make('last_exam_reference3')->label('sa ocjenom zdravstvene sposobnosti'),
                ])->columns(2),

            Section::make('Opis radnog mjesta')
                ->schema([
                    Textarea::make('short_description')
                        ->label('Kratak opis poslova')->rows(2)
                        ->maxLength(185)->extraAttributes(['maxlength' => 185])->rule('max:185')
                        ->reactive()
                        ->helperText(fn ($get) => mb_strlen((string) $get('short_description')) . '/185'),
                    Textarea::make('tools')
                        ->label('Strojevi, alati, aparati¹')->rows(1)
                        ->maxLength(80)->extraAttributes(['maxlength' => 80])->rule('max:80')
                        ->reactive()
                        ->helperText(fn ($get) => mb_strlen((string) $get('tools')) . '/80'),
                    Textarea::make('job_tasks')
                        ->label('Predmet rada²')->rows(1)
                        ->maxLength(80)->extraAttributes(['maxlength' => 80])->rule('max:80')
                        ->reactive()
                        ->helperText(fn ($get) => mb_strlen((string) $get('job_tasks')) . '/80'),
                ]),

            Section::make('Radni uvjeti – lokacija, organizacija i položaj')
                ->schema([
                    CheckboxList::make('workplace_location')
                        ->label('Mjesto rada:')
                        ->options([
                            'zatvorenom' => 'u zatvorenom',
                            'otvorenom' => 'na otvorenom',
                            'na_visini' => 'na visini',
                            'u_jami' => 'u jami',
                            'u_vodi' => 'u vodi',
                            'pod_vodom' => 'pod vodom',
                            'mokrim_uvjetima' => 'u mokrom',
                        ])
                        ->columns(7),

                    CheckboxList::make('organization')
                        ->label('Organizacija')
                        ->options([
                            'smjena'            => 'u smjenama',
                            'rad_na_traci'      => 'radi na traci',
                            'noćni'             => 'noćni rad',
                            'brzi_tempo'        => 'brzi tempo rada',
                            'terenski'          => 'terenski rad',
                            'ritam_određen'     => 'ritam određen',
                            'samostalni'        => 'radi sam',
                            'rad_sa_strankama'  => 'radi sa strankama',
                            'rad_s_grupom'      => 'radi s grupom',
                            'monotonija'        => 'monotonija',
                        ])
                        ->columns(5),

                    CheckboxList::make('body_position')
                        ->label('Položaj tijela i aktivnosti³:')
                        ->options([
                            'stojeći' => 'rad stojeći',
                            'u_pokretu' => 'u pokretu',
                            'sagibanje' => 'učestalo sagibanje',
                            'klečanje' => 'klečanje',
                            'podvlačenje' => 'podvlačenje',
                            'uspinjanje' => 'uspinjanje ljestvama',
                            'sjedeći' => 'rad sjedeći',
                            'kombinirano' => 'kombinirano',
                            'zakretanje' => 'zakretanje trupa',
                            'čučanje' => 'čučanje',
                            'balansiranje' => 'balansiranje',
                            'uspinjanje_stepenicama' => 'uspinjanje stepenicama',
                        ])
                        ->columns(6),

                    Grid::make(3)->schema([
                        Group::make([
                            Checkbox::make('lifting_enabled')->label('Dizanje tereta kg')->reactive(),
                            TextInput::make('lifting_weight')->label('')->placeholder('kg')
                                ->visible(fn (Closure $get) => $get('lifting_enabled')),
                        ]),
                        Group::make([
                            Checkbox::make('carrying_enabled')->label('Prenošenje tereta kg')->reactive(),
                            TextInput::make('carrying_weight')->label('')->placeholder('kg')
                                ->visible(fn (Closure $get) => $get('carrying_enabled')),
                        ]),
                        Group::make([
                            Checkbox::make('pushing_enabled')->label('Guranje tereta kg')->reactive(),
                            TextInput::make('pushing_weight')->label('')->placeholder('kg')
                                ->visible(fn (Closure $get) => $get('pushing_enabled')),
                        ]),
                    ]),

                    CheckboxList::make('job_characteristics')
                        ->label('U poslu je važan⁴:')
                        ->options([
                            'vid_na_daljinu' => 'vid na daljinu',
                            'vid_na_blizinu' => 'vid na blizinu',
                            'raspoznavanje'  => 'raspoznavanje boja',
                            'sluh'           => 'dobar sluh',
                            'govor'          => 'jasan govor',
                        ])
                        ->columns(5),

                    CheckboxList::make('hazards')
                        ->label('Uvjeti rada:')
                        ->options([
                            'toplina'     => 'visoka temperatura',
                            'vibracije'   => 'vibracije poda',
                            'vlažnost'    => 'visoka vlažnost',
                            'hladnoća'    => 'niska temperatura',
                            'vibracije1'  => 'vibracije stroja ili alata',
                            'zračenja'    => 'ionizirajuća zračenja',
                            'buka'        => 'buka',
                            'tlak'        => 'povišeni atmosferski tlak',
                            'ozljede'     => 'povećana izloženost ozljedama',
                            'zračenja1'   => 'neionizirajuća zračenja',
                            'prašina'     => 'prašina',
                        ])
                        ->columns(5),
                ]),

            Section::make('Kemijske tvari i biološke štetnosti')
                ->schema([
                    Textarea::make('chemcial_substances')
                        ->label('Kemijske tvari')->rows(1)
                        ->maxLength(70)->extraAttributes(['maxlength' => 70])->rule('max:70')
                        ->reactive()
                        ->helperText(fn ($get) => mb_strlen((string) $get('chemcial_substances')) . '/70'),
                    Textarea::make('biological_hazards')
                        ->label('Biološke štetnosti')->rows(1)
                        ->maxLength(70)->extraAttributes(['maxlength' => 70])->rule('max:70')
                        ->reactive()
                        ->helperText(fn ($get) => mb_strlen((string) $get('biological_hazards')) . '/70'),
                ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('display_name')
                ->label('Zaposlenik')
                ->getStateUsing(fn ($record) => $record->employee->name ?? $record->full_name)
                ->searchable() // bez custom query-ja
                ->sortable(),

            TextColumn::make('referral_number')->label('Broj uputnice')->sortable()->alignCenter()->searchable(),
            TextColumn::make('referral_date')->label('Datum')->date('d.m.Y.')->alignCenter()->sortable(),
            TextColumn::make('health_jobs_description')->label('Poslovi za koje se utvrđuje zdr. sposobnost')
                ->alignCenter()->wrap()->limit(150)->tooltip(fn ($record) => $record->health_jobs_description),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMedicalReferrals::route('/'),
            'create' => Pages\CreateMedicalReferral::route('/create'),
            'edit'   => Pages\EditMedicalReferral::route('/{record}/edit'),
            'view'   => Pages\ViewMedicalReferral::route('/{record}'),
        ];
    }
    /** Scope: admin sve, ostali samo svoje */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        if (! $user) {
            return $query->whereRaw('1=0');
        }

        return self::isAdminUser($user)
            ? $query
            : $query->where('user_id', $user->id);
    }

    /** Badge da prati isti scope kao i tablica */
    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (! $user) return '0';

        $q = static::getModel()::query();
        if (! self::isAdminUser($user)) {
            $q->where('user_id', $user->id);
        }

        return (string) $q->count();
    }


public static function getGlobalSearchEloquentQuery(): Builder
{
    return static::getEloquentQuery();
}

}
