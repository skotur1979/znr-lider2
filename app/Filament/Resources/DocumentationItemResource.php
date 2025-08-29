<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentationItemResource\Pages;
use App\Filament\Resources\DocumentationItemResource\RelationManagers;
use App\Models\DocumentationItem;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Auth;
use App\Traits\AutoAssignsUser; // isti trait kao u ostalim modulima

class DocumentationItemResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = DocumentationItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationLabel = 'Dokumentacija';
    protected static ?string $pluralModelLabel = 'Dokumentacija';
    protected static ?int $navigationSort = 11;

    // Admin vidi sve, ostali samo svoje
    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery();

        if (Auth::user()?->isAdmin()) {
            return $q;
        }

        return $q->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        // trait će ubaciti hidden user_id + tvoja polja
        return static::assignUserField(
            $form->schema(static::additionalFormFields())
        );
    }

    public static function additionalFormFields(): array
    {
        return [
            TextInput::make('naziv')->label('Naziv dokumenta')->required(),
            TextInput::make('tvrtka')->label('Tvrtka'),
            DatePicker::make('datum_izrade')->label('Datum izrade'),
            TextInput::make('status_napomena')->label('Status/Napomena'),
            FileUpload::make('prilozi')
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
                    $set('prilozi', []);
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
            TextColumn::make('naziv')->label('Naziv')->searchable(),
            TextColumn::make('tvrtka')->label('Tvrtka'),
            TextColumn::make('datum_izrade')->label('Datum izrade')->date(),
            TextColumn::make('status_napomena')->label('Status/Napomena'),
        ])
        ->filters([])
        ->actions([
            ViewAction::make('view')->label('Prikaz'),
            EditAction::make('edit')->label('Uredi'),
            DeleteAction::make('delete')->label('Obriši')
            ->label('Obriši')
    ->modalHeading('Obriši dokumentaciju')
    ->modalSubheading('Jeste li sigurni da želite obrisati ovu dokumentaciju?')
    ->successNotificationTitle('Dokumentacija je obrisana.'),
        ])
    ->bulkActions([
    DeleteBulkAction::make()
        ->modalHeading('Obriši dokumentaciju')
        ->modalSubheading('Jeste li sigurni da želite obrisati ovu dokumentaciju?')
        ->successNotificationTitle('Dokumentacija je obrisana.'),

        
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
            'index' => Pages\ListDocumentationItems::route('/'),
            'create' => Pages\CreateDocumentationItem::route('/create'),
            'edit' => Pages\EditDocumentationItem::route('/{record}/edit'),
        ];
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
