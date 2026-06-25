<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom_service')
                    ->label('Nom du service')
                    ->required()
                    ->maxLength(255),
                Select::make('bureau_id')
                    ->label('Bureau')
                    ->relationship('bureau', 'nom_bureau')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }
}
