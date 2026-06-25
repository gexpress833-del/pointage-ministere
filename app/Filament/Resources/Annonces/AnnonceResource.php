<?php

namespace App\Filament\Resources\Annonces;

use App\Filament\Resources\Annonces\Pages\CreateAnnonce;
use App\Filament\Resources\Annonces\Pages\EditAnnonce;
use App\Filament\Resources\Annonces\Pages\ListAnnonces;
use App\Models\Annonce;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AnnonceResource extends Resource
{
    protected static ?string $model = Annonce::class;

    protected static ?string $navigationLabel = 'Annonces';

    protected static ?string $modelLabel = 'Annonce';

    protected static ?string $pluralModelLabel = 'Annonces';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?string $slug = 'annonces';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('titre')
                ->label('Titre')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Select::make('niveau')
                ->label('Type d’annonce')
                ->options(Annonce::niveauOptions())
                ->default(Annonce::NIVEAU_INFO)
                ->required()
                ->native(false)
                ->helperText('Détermine la couleur sur le portail et dans l’admin : information (bleu), attention (ambre), urgence (rouge), rappel (violet).')
                ->columnSpanFull(),
            Textarea::make('contenu')
                ->label('Message')
                ->required()
                ->rows(10)
                ->columnSpanFull(),
            DateTimePicker::make('published_at')
                ->label('Date de publication')
                ->seconds(false)
                ->native(false)
                ->helperText('Vide = brouillon. Une date passée ou actuelle rend l’annonce visible.')
                ->columnSpanFull(),
            DateTimePicker::make('expires_at')
                ->label('Fin d\'affichage (optionnel)')
                ->seconds(false)
                ->native(false)
                ->helperText('Après cette date, l’annonce n’est plus affichée.')
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')->label('Titre')->searchable()->limit(40),
                TextColumn::make('niveau')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Annonce::niveauOptions()[$state ?? Annonce::NIVEAU_INFO] ?? ($state ?? '—'))
                    ->color(fn (?string $state): string => match ($state) {
                        Annonce::NIVEAU_ATTENTION => 'warning',
                        Annonce::NIVEAU_URGENCE => 'danger',
                        Annonce::NIVEAU_RAPPEL => 'primary',
                        default => 'info',
                    }),
                TextColumn::make('published_at')
                    ->label('Publication')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Brouillon')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expire')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('createdBy.nom')
                    ->label('Auteur')
                    ->default('—'),
                TextColumn::make('updated_at')
                    ->label('Mise à jour')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnonces::route('/'),
            'create' => CreateAnnonce::route('/create'),
            'edit' => EditAnnonce::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('createdBy');
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdministrateur() ?? false;
    }
}
