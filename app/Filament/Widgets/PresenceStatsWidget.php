<?php

namespace App\Filament\Widgets;

use App\Models\Presence;
use App\Models\SessionPresence;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PresenceStatsWidget extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.presence-stats-widget';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $today = Carbon::today();
        /** @var User|null $user */
        $user = Auth::user();

        $session = SessionPresence::where('date', $today)->first();

        $query = User::whereIn('role', [User::ROLE_AGENT, User::ROLE_CHEF_BUREAU]);

        if ($user && $user->isChefBureau() && $user->bureau_id) {
            $query->where('bureau_id', $user->bureau_id);
        }

        $totalAgents = $query->count();

        if ($totalAgents === 0) {
            $totalAgents = User::count();
        }

        $presents = 0;
        $retards = 0;
        $aTemps = 0;

        if ($session) {
            $presencesQuery = $session->presences();

            if ($user && $user->isChefBureau() && $user->bureau_id) {
                $presencesQuery->whereHas('user', fn ($q) => $q->where('bureau_id', $user->bureau_id));
            }

            $presents = $presencesQuery->count();
            $retards = $presencesQuery->where('statut', Presence::STATUT_RETARD)->count();
            $aTemps = $presents - $retards;
        }

        return [
            'sessionLabel' => $session
                ? ($session->isOuverte() ? 'Session ouverte aujourd\'hui' : 'Session clôturée')
                : 'Aucune session aujourd\'hui',
            'cards' => [
                [
                    'label' => 'Agents suivis',
                    'value' => $totalAgents,
                    'description' => $session ? 'Population active du jour' : 'Base d\'agents concernée',
                    'tone' => 'blue',
                ],
                [
                    'label' => 'Présents à l\'heure',
                    'value' => $aTemps,
                    'description' => $presents > 0 ? 'sur '.$presents.' signatures' : 'aucune signature',
                    'tone' => 'green',
                ],
                [
                    'label' => 'Retardataires',
                    'value' => $retards,
                    'description' => 'arrivés après l\'heure limite',
                    'tone' => 'amber',
                ],
                [
                    'label' => 'Absents',
                    'value' => max(0, $totalAgents - $presents),
                    'description' => 'non signés pour la session',
                    'tone' => 'red',
                ],
            ],
        ];
    }
}
