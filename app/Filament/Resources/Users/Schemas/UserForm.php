<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nom affichage')->required()->maxLength(255),
            TextInput::make('nom')->label('Nom complet')->required()->maxLength(255),
            TextInput::make('matricule')->label('Matricule')->required()->unique(ignoreRecord: true)->maxLength(50),
            TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true)->maxLength(255),
            TextInput::make('telephone')->label('Telephone')->tel()->maxLength(50),
            Textarea::make('adresse_residence')
                ->label('Adresse de résidence')
                ->rows(3)
                ->maxLength(2000)
                ->nullable()
                ->helperText('Visible et modifiable par l’utilisateur sur le portail présence.'),
            Placeholder::make('photo_reference_preview')
                ->label('Photo actuelle')
                ->visible(fn (?User $record): bool => filled($record?->photo_reference))
                ->content(function (?User $record): HtmlString {
                    if (! $record || ! $record->photo_reference) {
                        return new HtmlString('');
                    }

                    $url = route('users.photo-reference', $record);

                    return new HtmlString(<<<HTML
                        <div style="display:flex;align-items:center;gap:16px;padding:12px 0;">
                            <img src="{$url}" alt="Photo actuelle" style="width:88px;height:88px;border-radius:18px;object-fit:cover;border:1px solid rgba(148,163,184,.25);display:block;">
                            <div style="font-size:12px;line-height:1.5;color:#94a3b8;">
                                <div style="font-weight:600;color:#e2e8f0;margin-bottom:4px;">Photo de référence enregistrée</div>
                                <div>Cette image est utilisée pour la reconnaissance faciale.</div>
                            </div>
                        </div>
                    HTML);
                }),
            FileUpload::make('photo_reference')
                ->label('Photo de référence (visage)')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                ->directory('photos_reference')
                ->visibility('private')
                ->disk('local')
                ->fetchFileInformation(false)
                ->previewable(false)
                ->required(fn (string $operation): bool => $operation === 'create')
                ->helperText(fn (string $operation): string => $operation === 'edit' ? 'Laisser vide pour conserver la photo actuelle.' : 'Photo du visage (JPEG, PNG, GIF ou WebP), utilisée pour la reconnaissance faciale.')
                ->imageEditor(),
            Select::make('bureau_id')->label('Bureau')->relationship('bureau', 'nom_bureau')->searchable()->preload()->nullable(),
            Select::make('service_id')->label('Service')->relationship('service', 'nom_service')->searchable()->preload()->nullable(),
            Select::make('role')->label('Role')->options([User::ROLE_ADMIN => 'Administrateur', User::ROLE_COORDINATEUR => 'Coordinateur', User::ROLE_CHEF_BUREAU => 'Chef de bureau', User::ROLE_AGENT => 'Agent'])->required(),
            TextInput::make('password')
                ->label('Mot de passe')
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated()
                ->helperText(fn (string $operation): string => $operation === 'edit' ? 'Laisser vide pour conserver le mot de passe actuel.' : '')
                ->maxLength(255),
        ]);
    }
}
