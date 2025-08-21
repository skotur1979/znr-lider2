<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MiscellaneousResource\Pages;
use App\Filament\Resources\MiscellaneousResource\RelationManagers;
use App\Models\Miscellaneous;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use App\Models\Category;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\FileUpload;
use App\Traits\AutoAssignsUser;

class MiscellaneousResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = Miscellaneous::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationLabel = 'Ostala Ispitivanja';

    protected static ?string $modelLabel = 'Ispitivanja';

    protected static ?string $pluralModelLabel = 'Ispitivanja';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationGroup = 'Moduli';

    public static function form(Form $form): Form
{
    return static::assignUserField($form);
}
            public static function additionalFormFields(): array
{
    return [
                Section::make('Podatci o predmetu')->schema([
                    TextInput::make('name')->label('Naziv (obavezno)')->prefixIcon('heroicon-o-cog')->string()->filled(),
                    Select::make('category_id')
                            ->label('Kategorija')
                            ->options(Category::all()->pluck('name','id')->toArray())
                            ->default(1)
                            ->disablePlaceholderSelection()
                            ->searchable()
                            ->filled(),
                    TextInput::make('examiner')->label('Ispitao')->prefixIcon('heroicon-o-cog')->nullable(),
                    TextInput::make('report_number')->label('Broj izvještaja')->prefixIcon('heroicon-o-document-duplicate')->nullable(),

                ])->columns(2),

                Section::make('Ispitivanje')->schema([
                    DatePicker::make('examination_valid_from')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi od (obavezno)')->filled(),
                    DatePicker::make('examination_valid_until')->format('Y-m-d')->displayFormat('d.m.Y.')->weekStartsOnMonday()->timezone('Europe/Zagreb')->label('Vrijedi do (obavezno)')->filled(),
                ])->columns(2),

                Section::make('Napomena')->schema([
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
                TextColumn::make('name')->searchable()->sortable()->weight('bold')->wrap()->label('Naziv'),
                BadgeColumn::make('category_id')->enum(Category::all()->pluck('name','id')->toArray())->label('Kategorija')->alignCenter()->sortable(),
                TextColumn::make('examiner')->sortable()->size('sm')->label('Ispitao')->alignCenter(),

                TextColumn::make('examination_valid_from')->date('d.m.Y')->sortable()->alignCenter()->label('Datum ispitivanja'),

                BadgeColumn::make('examination_valid_until')
                ->date('d.m.Y.')->alignCenter()
                ->label('Ispitivanje vrijedi do')
                ->colors([
                    'success'   => static fn ($date):bool => $date->diffInDays(Carbon::today()) > 30,
                    'warning'   => static fn ($date):bool => $date->diffInDays(Carbon::today()) <= 30,
                    'danger'    => static fn ($date):bool => $date->lt(Carbon::today())
                ])->sortable(),
                TextColumn::make('remark')->searchable()->sortable()->size('sm')->label('Napomena'),
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
                SelectFilter::make('category_id')->options(Category::all()->pluck('name', 'id')->toArray())->label('Kategorije'),
                Filter::make('examination_validity_expired')->label('Ispitivanje (isteklo)')->query(fn (Builder $query): Builder => $query->where('examination_valid_until', '<', Carbon::today())),
                Filter::make('examination_validity_expiring')->label('Ispitivanje (uskoro ističe)')->query(fn (Builder $query): Builder => $query->where('examination_valid_until', '>', Carbon::today())->where('examination_valid_until','<', Carbon::today()->addMonth())),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->requiresConfirmation()
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
            'index' => Pages\ListMiscellaneouses::route('/'),
            'create' => Pages\CreateMiscellaneous::route('/create'),
            'view' => Pages\ViewMiscellaneous::route('/{record}'),
            'edit' => Pages\EditMiscellaneous::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->where('user_id', auth()->id())
        ->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
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
