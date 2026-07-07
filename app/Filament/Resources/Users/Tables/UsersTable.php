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
            ->toolbarActions($canDelete ? [BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->action(function (\Illuminate\Support\Collection $records) {
                        $deleted = 0;
                        $skipped = 0;
                        $records->each(function ($record) use (&$deleted, &$skipped) {
                            if (! $record->isProtectedAdmin()) {
                                $record->delete();
                                $deleted++;
                            } else {
                                $skipped++;
                            }
                        });

                        if ($deleted > 0 && $skipped > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('Suppression partielle')
                                ->body("{$deleted} utilisateur(s) supprimé(s). {$skipped} admin principal protégé non supprimé.")
                                ->warning()
                                ->send();
                        } elseif ($deleted > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('Utilisateurs supprimés')
                                ->body("{$deleted} utilisateur(s) supprimé(s) avec succès.")
                                ->success()
                                ->send();
                        } elseif ($skipped > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('Aucune suppression')
                                ->body('L\'administrateur principal ne peut pas être supprimé.')
                                ->danger()
                                ->send();
                        }
                    }),
            ])] : []);
    }
}
