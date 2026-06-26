<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ViewColumn::make('photo_reference')
                    ->label('Photo')
                    ->view('filament.tables.columns.user-photo'),
                TextColumn::make('nom')->label('Nom')->searchable()->sortable(),
                TextColumn::make('matricule')->label('Matricule')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('bureau.nom_bureau')->label('Bureau'),
                TextColumn::make('role')->label('Role')->badge(),
            ])
            ->filters([])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
