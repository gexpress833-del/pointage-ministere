<?php

namespace App\Filament\Resources\SessionsPresence;

use App\Filament\Resources\SessionsPresence\Pages\ListSessionsPresence;
use App\Models\SessionPresence;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SessionPresenceResource extends Resource
{
    protected static ?string $model = SessionPresence::class;

    protected static ?string $navigationLabel = 'Sessions de présence';

    protected static ?string $modelLabel = 'Session';

    protected static ?string $pluralModelLabel = 'Sessions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $slug = 'sessions-presence';

    protected static string|\UnitEnum|null $navigationGroup = 'Présence';

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                TextColumn::make('date')->label('Date')->date('d/m/Y')->sortable(),
                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state): string => $state === SessionPresence::STATUT_OUVERTE ? 'success' : 'gray'),
                TextColumn::make('openedBy.nom')->label('Ouverte par')->default('—'),
                TextColumn::make('closedBy.nom')->label('Fermée par')->default('—'),
                TextColumn::make('presences_count')->label('Signatures')->counts('presences'),
            ])
            ->defaultSort('date', 'desc')
            ->recordActions([
                Action::make('fermer')
                    ->label('Fermer')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (SessionPresence $record) => $record->isOuverte() && Auth::user()?->isAdministrateur())
                    ->action(function (SessionPresence $record) {
                        $record->update([
                            'statut' => SessionPresence::STATUT_FERMEE,
                            'closed_by' => Auth::id(),
                            'closed_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation(),
                Action::make('pdf')
                    ->label('PDF journalier')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->url(fn (SessionPresence $record) => route('reports.daily', ['session' => $record->id]))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSessionsPresence::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user && in_array($user->role, [
            User::ROLE_ADMIN,
            User::ROLE_COORDINATEUR,
            User::ROLE_CHEF_BUREAU,
        ]);
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isAdministrateur() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'openedBy:id,nom,name',
                'closedBy:id,nom,name',
            ]);
    }
}
