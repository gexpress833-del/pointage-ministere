<?php

namespace App\Filament\Resources\SessionsPresence\Pages;

use App\Filament\Resources\SessionsPresence\SessionPresenceResource;
use App\Models\Bureau;
use App\Models\Parametre;
use App\Models\Presence;
use App\Models\SessionPresence;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                    DatePicker::make('date_reset')
                        ->label('Date à réinitialiser')
                        ->required()
                        ->native(true),
                    Checkbox::make('delete_session')
                        ->label('Supprimer aussi la session de cette journée (définitif — la journée disparaît complètement)')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $dateReset = $data['date_reset'] ?? null;
                    $deleteSession = ! empty($data['delete_session']);

                    if (! $dateReset) {
                        \Filament\Notifications\Notification::make()
                            ->title('Date manquante')
                            ->body('Veuillez sélectionner une date.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $session = SessionPresence::where('date', $dateReset)->first();

                    if (! $session) {
                        \Filament\Notifications\Notification::make()
                            ->title('Aucune session')
                            ->body('Aucune session trouvée pour le '.$dateReset.'.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $presencesCount = Presence::where('session_id', $session->id)->count();

                    DB::transaction(function () use ($session, $deleteSession) {
                        Presence::where('session_id', $session->id)->delete();

                        if ($deleteSession) {
                            $session->delete();
                        } else {
                            $session->update([
                                'statut' => SessionPresence::STATUT_OUVERTE,
                                'opened_by' => Auth::id(),
                                'opened_at' => now(),
                                'closed_by' => null,
                                'closed_at' => null,
                            ]);
                        }
                    });

                    $msg = $deleteSession
                        ? $presencesCount.' pointage(s) supprimé(s) et session du '.$dateReset.' effacée définitivement. Historiques et rapports remis à zéro.'
                        : $presencesCount.' pointage(s) supprimé(s) pour le '.$dateReset.'. Session rouverte. Historiques et rapports remis à zéro.';

                    \Filament\Notifications\Notification::make()
                        ->title('Réinitialisation effectuée')
                        ->body($msg)
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
