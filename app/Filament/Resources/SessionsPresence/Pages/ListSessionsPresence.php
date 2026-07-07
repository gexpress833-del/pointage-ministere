<?php

namespace App\Filament\Resources\SessionsPresence\Pages;

use App\Filament\Resources\SessionsPresence\SessionPresenceResource;
use App\Models\Bureau;
use App\Models\SessionPresence;
use Carbon\Carbon;
use Filament\Actions\Action;
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
                ->label('Ouvrir session aujourd\'hui')
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->visible(fn () => Auth::user()?->isAdministrateur() || Auth::user()?->isSecretaire())
                ->authorize(fn () => Auth::user()?->isAdministrateur() || Auth::user()?->isSecretaire() ?? false)
                ->action(function () {
                    $today = Carbon::today();
                    if (SessionPresence::where('date', $today)->exists()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Session existante')
                            ->body('Une session existe déjà pour aujourd\'hui.')
                            ->warning()
                            ->send();
                        return;
                    }
                    SessionPresence::create([
                        'date' => $today,
                        'statut' => SessionPresence::STATUT_OUVERTE,
                        'opened_by' => Auth::id(),
                    ]);
                    \Filament\Notifications\Notification::make()
                        ->title('Session ouverte')
                        ->body('La session de présence pour aujourd\'hui a été ouverte avec succès.')
                        ->success()
                        ->send();
                }),

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
