<?php

namespace App\Filament\Resources\SessionsPresence\Pages;

use App\Filament\Resources\SessionsPresence\SessionPresenceResource;
use App\Models\Bureau;
use App\Models\Parametre;
use App\Models\Presence;
use App\Models\SessionPresence;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListSessionsPresence extends ListRecords
{
    protected static string $resource = SessionPresenceResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        return [
            Action::make('ouvrir_session')
                ->label('Ouvrir / Réouvrir session aujourd\'hui')
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->visible(fn () => $user && ($user->isAdministrateur() || $user->isSecretaire()))
                ->action(function () {
                    $today = Carbon::today();
                    $session = SessionPresence::where('date', $today)->first();

                    if (! $session) {
                        SessionPresence::create([
                            'date' => $today,
                            'statut' => SessionPresence::STATUT_OUVERTE,
                            'opened_by' => Auth::id(),
                            'opened_at' => now(),
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Session ouverte')
                            ->body('La session de présence pour aujourd\'hui a été ouverte avec succès.')
                            ->success()
                            ->send();
                        return;
                    }

                    if ($session->isOuverte()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Session déjà ouverte')
                            ->body('La session d\'aujourd\'hui est déjà ouverte.')
                            ->warning()
                            ->send();
                        return;
                    }

                    if (! Parametre::sessionAllowReopen()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Réouverture désactivée')
                            ->body('La réouverture des sessions est désactivée dans les paramètres.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $session->reopen(Auth::id());
                    \Filament\Notifications\Notification::make()
                        ->title('Session réouverte')
                        ->body('La session d\'aujourd\'hui a été réouverte avec succès.')
                        ->success()
                        ->send();
                }),

            Action::make('reset_presences')
                ->label('Réinitialiser les pointages')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->visible(fn () => $user && $user->isAdministrateur())
                ->form([
                    Select::make('session_id')
                        ->label('Session (journée)')
                        ->options(function () {
                            return SessionPresence::orderBy('date', 'desc')
                                ->limit(30)
                                ->get()
                                ->mapWithKeys(fn ($s) => [$s->id => $s->date->format('d/m/Y').' ('.$s->statut.')'])
                                ->toArray();
                        })
                        ->placeholder('Toutes les sessions de la période')
                        ->nullable(),
                    DatePicker::make('date_debut')
                        ->label('Date de début (optionnel)')
                        ->nullable(),
                    DatePicker::make('date_fin')
                        ->label('Date de fin (optionnel)')
                        ->nullable(),
                ])
                ->action(function (array $data) {
                    if (! Parametre::sessionAllowResetPresences()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Action désactivée')
                            ->body('La réinitialisation des pointages est désactivée dans les paramètres.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $query = Presence::query();

                    if (! empty($data['session_id'])) {
                        $query->where('session_id', $data['session_id']);
                    } elseif (! empty($data['date_debut']) || ! empty($data['date_fin'])) {
                        $sessionIds = SessionPresence::query();
                        if (! empty($data['date_debut'])) {
                            $sessionIds->where('date', '>=', $data['date_debut']);
                        }
                        if (! empty($data['date_fin'])) {
                            $sessionIds->where('date', '<=', $data['date_fin']);
                        }
                        $query->whereIn('session_id', $sessionIds->pluck('id'));
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Aucun critère')
                            ->body('Veuillez sélectionner une session ou une période.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $count = $query->count();
                    $query->delete();

                    \Filament\Notifications\Notification::make()
                        ->title('Pointages réinitialisés')
                        ->body($count.' enregistrement(s) de présence supprimé(s).')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),

            Action::make('rapport_mensuel')
                ->label('Rapport mensuel PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->visible(fn () => $user && ($user->isAdministrateur() || $user->isCoordinateur() || $user->isChefBureau()))
                ->form(function () use ($user): array {
                    $bureaux = Bureau::orderBy('nom_bureau')->pluck('nom_bureau', 'id')->toArray();
                    $fields = [
                        Select::make('year')
                            ->label('Année')
                            ->options(array_combine(
                                range(now()->year, now()->year - 3),
                                range(now()->year, now()->year - 3)
                            ))
                            ->default(now()->year)
                            ->required(),
                        Select::make('month')
                            ->label('Mois')
                            ->options([
                                1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
                                4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                                7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
                                10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
                            ])
                            ->default(now()->month)
                            ->required(),
                    ];

                    if ($user->isAdministrateur() || $user->isCoordinateur()) {
                        $fields[] = Select::make('bureau_id')
                            ->label('Bureau (optionnel)')
                            ->options($bureaux)
                            ->placeholder('Tous les bureaux')
                            ->nullable();
                    }

                    return $fields;
                })
                ->action(function (array $data) use ($user): void {
                    $bureauId = ($user->isChefBureau()) ? $user->bureau_id : ($data['bureau_id'] ?? null);
                    $url = route('reports.monthly', array_filter([
                        'year' => $data['year'],
                        'month' => $data['month'],
                        'bureau_id' => $bureauId,
                    ]));
                    $this->redirect($url, navigate: false);
                }),
        ];
    }
}
