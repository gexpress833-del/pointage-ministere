<?php

namespace App\Filament\Resources\Parametres;

use App\Filament\Resources\Parametres\Pages\EditParametre;
use App\Filament\Resources\Parametres\Pages\ListParametres;
use App\Models\Parametre;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParametreResource extends Resource
{
    protected static ?string $model = Parametre::class;

    protected static ?string $navigationLabel = 'Paramètres';

    protected static ?string $modelLabel = 'Paramètre';

    protected static ?string $pluralModelLabel = 'Paramètres';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $slug = 'parametres';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('cle')->label('Clé')->required()->maxLength(255)->disabled(fn (?Parametre $record) => $record !== null),
            Select::make('type')
                ->label('Type')
                ->options(['string' => 'Texte', 'integer' => 'Entier', 'boolean' => 'Oui/Non', 'time' => 'Heure (HH:MM)'])
                ->required()
                ->live(),
            TextInput::make('valeur')
                ->label('Valeur')
                ->required()
                ->visible(fn (Get $get): bool => $get('type') !== 'time'),
            TimePicker::make('valeur')
                ->label('Valeur')
                ->required()
                ->seconds(false)
                ->format('H:i')
                ->visible(fn (Get $get): bool => $get('type') === 'time'),
            TextInput::make('description')->label('Description')->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cle')->label('Clé')->searchable(),
                TextColumn::make('valeur')->label('Valeur'),
                TextColumn::make('type')->label('Type')->badge(),
                TextColumn::make('description')->label('Description')->limit(40),
            ])
            ->recordActions([EditAction::make()])
            ->emptyStateHeading('Aucun paramètre enregistré')
            ->emptyStateDescription('Les paramètres par défaut sont créés lors de l\'installation (php artisan db:seed --class=ParametresSeeder).')
            ->defaultSort('cle');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParametres::route('/'),
            'edit' => EditParametre::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdministrateur() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
