<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Presence;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        $user = Auth::user();
        $isChefBureau = $user && $user->isChefBureau();
        $canDelete = $user && $user->isAdministrateur();

        return $table
            ->columns([
                ViewColumn::make('photo_reference')
                    ->label('Photo')
                    ->view('filament.tables.columns.user-photo'),
                TextColumn::make('nom')->label('Nom')->searchable()->sortable(),
                TextColumn::make('matricule')->label('Matricule')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('bureau.nom_bureau')->label('Bureau')
                    ->visible(! $isChefBureau),
                TextColumn::make('role')->label('Role')->badge()
                    ->visible(! $isChefBureau),
                TextColumn::make('presences_present_count')
                    ->label('Présents')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('presences_retard_count')
                    ->label('Retards')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('presences_total_count')
                    ->label('Total signatures')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([EditAction::make()])
            ->toolbarActions($canDelete ? [BulkActionGroup::make([DeleteBulkAction::make()])] : []);
    }
}
