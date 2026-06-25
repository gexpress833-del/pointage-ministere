<?php

namespace App\Filament\Resources\Bureaus\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BureauForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom_bureau')
                    ->label('Nom du bureau')
                    ->required()
                    ->maxLength(255),
                Select::make('chef_bureau_id')
                    ->label('Chef de bureau')
                    ->relationship(
                        query: fn ($query) => $query->whereIn('role', [User::ROLE_CHEF_BUREAU, User::ROLE_AGENT])
                    )
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->getDisplayName().' ('.$record->matricule.')')
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }
}
