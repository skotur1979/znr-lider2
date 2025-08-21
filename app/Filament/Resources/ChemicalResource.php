<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChemicalResource\Pages;
use App\Models\Chemical;
use Filament\Forms;
use Filament\Forms\Components\{TextInput, Textarea, TagsInput, DatePicker, Grid};
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Tables\Filters\TrashedFilter;
use App\Enums\HazardStatement;
use App\Enums\PrecautionaryStatement;
use Filament\Forms\Components\Select;



class ChemicalResource extends Resource
{
    protected static ?string $model = Chemical::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Kemikalije';
    protected static ?string $pluralModelLabel = 'Kemikalije';
    protected static ?int $navigationSort = 6;

    protected static ?string $navigationGroup = 'Moduli';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('product_name')->label('Ime proizvoda')->required(),
                TextInput::make('cas_number')->label('CAS broj'),
                TextInput::make('ufi_number')->label('UFI broj'),
                TagsInput::make('hazard_pictograms')
                    ->label('Piktogrami opasnosti')
                    ->suggestions([
                        'GHS01', 'GHS02', 'GHS03', 'GHS04',
                        'GHS05', 'GHS06', 'GHS07', 'GHS08', 'GHS09'
                    ])
                    ->placeholder('npr. GHS05, GHS07'),
                    Select::make('h_statements')
    ->label('H oznake (opasnosti)')
    ->options(\App\Enums\HazardStatement::list())
    ->searchable()
    ->multiple()
    ->nullable()
    ->default([]),

Select::make('p_statements')
    ->label('P oznake (mjere opreza)')
    ->options(\App\Enums\PrecautionaryStatement::list())
    ->searchable()
    ->multiple()
    ->nullable()
    ->default([]),
                Textarea::make('h_statements')->label('Oznake upozorenja H'),
                Textarea::make('p_statements')->label('Oznake opasnosti P'),
                TextInput::make('usage_location')->required()->label('Mjesto upotrebe'),
                TextInput::make('annual_quantity')->label('Godišnje količine (kg/l)'),
                TextInput::make('gvi_kgvi')->label('GVI / KGVI'), // <-- dodano ovo novo polje
                TextInput::make('voc')
                ->label('Hlapljivi organski spojevi (VOC)'),
                DatePicker::make('stl_hzjz')->label('STL – HZJZ'),
                // 📁 Upload polje za priloge
                FileUpload::make('attachments')
                ->label('Prilozi')
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
            $set('attachments', []);
            \Filament\Notifications\Notification::make()
                ->title("Ukupna veličina datoteka ne smije biti veća od {$maxTotalMB} MB.")
                ->danger()
                ->persistent()
                ->send();
           }
    }),
            ]),
        ]);
}
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_name')->size('sm')->label('Ime proizvoda')->wrap()->tooltip(fn ($record) => $record->product_name)->searchable(),
                TextColumn::make('cas_number')->size('sm')->label('CAS')->wrap()->tooltip(fn ($record) => $record->cas_number),
                TextColumn::make('ufi_number')->size('sm')->label('UFI')->wrap()->tooltip(fn ($record) => $record->ufi_number),
                ViewColumn::make('hazard_pictograms')
                    ->label('Piktogrami')->alignCenter()
                    ->view('components.table.hazard-pictograms'),
                    TextColumn::make('h_statements')
    ->label('H oznake')->size('sm')
    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
    ->wrap()->alignCenter()->tooltip(fn ($record) => is_array($record->h_statements) ? implode(', ', $record->h_statements) : $record->h_statements),

TextColumn::make('p_statements')
    ->label('P oznake')->size('sm')
    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
    ->wrap()->alignCenter()->tooltip(fn ($record) => is_array($record->p_statements) ? implode(', ', $record->p_statements) : $record->p_statements),
                TextColumn::make('usage_location')->alignCenter()->wrap()->label('Mjesto upotrebe'),
                TextColumn::make('annual_quantity')->alignCenter()->wrap()->label('Količina'),
                TextColumn::make('gvi_kgvi')->alignCenter()->wrap()->label('GVI / KGVI'),
                TextColumn::make('voc')->alignCenter()->wrap()->label('VOC'),
                TextColumn::make('stl_hzjz')->alignCenter()->label('STL – HZJZ')->date(),
                BadgeColumn::make('attachments')
    ->label('Prilozi')
    ->icon(fn ($record) =>
        is_array($record->attachments) && count($record->attachments) > 0
            ? 'heroicon-o-paper-clip'
            : null
    )
    ->color(fn ($record) =>
        is_array($record->attachments) && count($record->attachments) > 0
            ? 'info'
            : 'gray'
    )
    ->tooltip(fn ($record) =>
        is_array($record->attachments) && count($record->attachments) > 0
            ? implode("\n", $record->attachments)
            : 'Nema priloga'
    )
    ->alignCenter()
    ->formatStateUsing(fn ($state, $record) =>
        is_array($record->attachments) ? (string) count($record->attachments) : '0'
    )

            ])
            ->filters([
            TrashedFilter::make(), // ➤ Ovo dodaje filter: "Aktivni / Deaktivirani / Svi"
            SelectFilter::make('usage_location')
                ->label('Mjesto upotrebe')
                ->options(fn () => Chemical::query()
                    ->select('usage_location')
                    ->distinct()
                    ->pluck('usage_location', 'usage_location')),
        ])
        ->actions([
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Deaktiviraj')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation(),
                Tables\Actions\RestoreAction::make()->requiresConfirmation(),
                Tables\Actions\ForceDeleteAction::make()->requiresConfirmation(),
            ]),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
            Tables\Actions\RestoreBulkAction::make(),
            Tables\Actions\ForceDeleteBulkAction::make(),
        ]);
    }                  
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChemicals::route('/'),
            'create' => Pages\CreateChemical::route('/create'),
            'edit' => Pages\EditChemical::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
}
}
