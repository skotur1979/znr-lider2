<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FirstAidKitResource\Pages;
use App\Models\FirstAidKit;
use Filament\Forms;
use Filament\Forms\Components\{TextInput, Textarea, DatePicker, Repeater, Section};
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Support\Actions\Modal\Actions\CancelAction;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Auth;
use App\Traits\AutoAssignsUser;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class FirstAidKitResource extends Resource
{
    use AutoAssignsUser;
    protected static ?string $model = FirstAidKit::class;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $navigationGroup = 'Ispitivanja';
    protected static ?string $pluralModelLabel = 'Prva pomoć';
    protected static ?string $navigationLabel = 'Prva pomoć - ormarići';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
{
    return static::assignUserField($form);
}

public static function additionalFormFields(): array
{
    return [
                Section::make('Sanitetski materijal za prvu pomoć')
                    ->schema([
                        TextInput::make('location')->label('Lokacija ormarića PP')->required(),
                        DatePicker::make('inspected_at')->label('Pregled obavljen dana')->required(),
                        Textarea::make('note')->label('Napomena')->rows(2),
                    ]),

                Section::make('Sadržaj ormarića prve pomoći')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->label('Sanitetski materijal')
                            ->schema([
                                TextInput::make('material_type')->label('Vrsta sanitetskog materijala')->required(),
                                TextInput::make('purpose')->label('Namjena'),
                                DatePicker::make('valid_until')->label('Vrijedi do'),
                            ])
                            ->columns(3)
                            ->createItemButtonLabel('Dodaj stavku')
                            ->defaultItems(1),
                    ]),
            ];
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('location')->label('Lokacija ormarića')->sortable()->searchable(),
            TextColumn::make('inspected_at')->alignCenter()->label('Pregled obavljen')->date(),
        TextColumn::make('items_count')->alignCenter()->label('Ukupan broj stavki')->counts('items'),
        ViewColumn::make('items_summary')
            ->label('Rok ističe/istekao')->alignCenter()
            ->view('filament.resources.first-aid-kits.items_summary')
            ->extraAttributes(['class' => 'text-center']),
    ])
        ->actions([
            ViewAction::make('view')->label('Prikaz'),
            EditAction::make('edit')->label('Uredi'),
            DeleteAction::make('delete')->label('Obriši')
            ->modalHeading('Obriši Prvu pomoć')
    ->modalSubheading('Jeste li sigurni da želite obrisati ovu Prvu pomoć?')
    ->successNotificationTitle('Prva pomoć je obrisana.'),
        ])
    ->bulkActions([
    DeleteBulkAction::make()
        ->modalHeading('Obriši Prve pomoći')
        ->modalSubheading('Jeste li sigurni da želite obrisati ove Prve pomoći?')
        ->successNotificationTitle('Prve pomoći su obrisane.'),

        ])
        ->defaultSort('inspected_at', 'desc');
}

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFirstAidKits::route('/'),
            'create' => Pages\CreateFirstAidKit::route('/create'),
            'edit' => Pages\EditFirstAidKit::route('/{record}/edit'),
            
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
        if (!Auth::user()?->isAdmin()) {
            $q->where('user_id', Auth::id());
        }
        return (string) $q->count();
    }

    /** dodatna sigurnost da se user_id upiše pri kreiranju i update-u */
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