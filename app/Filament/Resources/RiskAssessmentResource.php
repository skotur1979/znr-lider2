<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiskAssessmentResource\Pages;
use App\Models\RiskAssessment;
use Filament\Resources\Resource;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\{ViewAction, EditAction, DeleteAction};
use Filament\Tables\Actions\DeleteBulkAction;

class RiskAssessmentResource extends Resource
{
    protected static ?string $model = RiskAssessment::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    protected static ?string $pluralModelLabel = 'Procjene rizika';
    protected static ?string $navigationLabel = 'Procjene rizika';
    protected static ?string $navigationGroup = 'Moduli';
    protected static ?string $modelLabel = 'Procjene rizika';


    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->with(['participants', 'revisions', 'attachments']);
}

    public static function form(Form $form): Form
    {
        return $form
    ->schema([

        // 🔷 Sekcija: Podaci o procjeni rizika
        Forms\Components\Section::make('Podaci o procjeni rizika')
            ->schema([
                Forms\Components\TextInput::make('tvrtka')->required()->label('Tvrtka'),
                Forms\Components\TextInput::make('oib_tvrtke')->nullable()->label('OIB tvrtke'),
                Forms\Components\TextInput::make('adresa_tvrtke')->label('Adresa tvrtke'),
                Forms\Components\TextInput::make('broj_procjene')->required()->label('Broj procjene'),
                Forms\Components\DatePicker::make('datum_izrade')->required()->label('Datum izrade'),
                Forms\Components\TextInput::make('vrsta_procjene')->label('Vrsta procjene rizika')->required(),
            ])
            ->columns(3)
            ->collapsible(),

        // 🧑‍💼 Sekcija: Sudionici izrade
        Forms\Components\Section::make('Sudionici izrade')
            ->schema([
                Forms\Components\Repeater::make('participants')
                    ->label('Sudionici izrade')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('ime_prezime')->nullable()->label('Ime i prezime'),
                        Forms\Components\TextInput::make('uloga')->nullable()->label('Uloga'),
                        Forms\Components\Textarea::make('napomena')->label('Napomena')->rows(1)->columnSpan(1),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ])
            ->collapsible(),

        // 🧾 Sekcija: Revizije
        Forms\Components\Section::make('Revizije Procjene Rizika')
            ->schema([
                Forms\Components\Repeater::make('revisions')
                    ->label('Revizije')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('revizija_broj')->nullable()->label('Revizija broj'),
                        Forms\Components\DatePicker::make('datum_izrade')->nullable()->label('Datum izrade'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ])
            ->collapsible(),

        // 📎 Sekcija: Prilozi
        Forms\Components\Section::make('Prilozi')
            ->schema([
                Forms\Components\Repeater::make('attachments')
                    ->label('Prilozi')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('naziv')
                            ->label('Naziv dokumenta')
                            ->required(),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Dokument')
                            ->directory('procjene-rizika')
                            ->preserveFilenames()
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ])
            ->collapsible(),

    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('tvrtka')->searchable(),
            TextColumn::make('broj_procjene')->alignCenter(),
            TextColumn::make('datum_izrade')->alignCenter()->date(),
            TextColumn::make('vrsta_procjene')->alignCenter()->label('Vrsta procjene')->searchable(),

            // 🔽 Nova kolona: broj revizija
            TextColumn::make('revisions_count')
                ->label('Broj revizija')->alignCenter()
                ->counts('revisions'), // automatski prebrojava
        ])
        ->actions([
            ViewAction::make('view')->label('Prikaz'),
            EditAction::make('edit')->label('Uredi'),
            DeleteAction::make('delete')->label('Obriši')
    ->modalHeading('Obriši Procjenu rizika')
    ->modalSubheading('Jeste li sigurni da želite obrisati ovu Procjenu rizika?')
    ->successNotificationTitle('Procjena rizika je obrisana.'),
        ])
    ->bulkActions([
    DeleteBulkAction::make()
        ->modalHeading('Obriši Procjene rizika')
        ->modalSubheading('Jeste li sigurni da želite obrisati ove Procjene rizika?')
        ->successNotificationTitle('Procjene rizika su obrisane.'),

        ]);
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiskAssessments::route('/'),
            'create' => Pages\CreateRiskAssessment::route('/create'),
            'edit' => Pages\EditRiskAssessment::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}