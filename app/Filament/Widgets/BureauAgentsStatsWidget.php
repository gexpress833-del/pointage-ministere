<?php

namespace App\Filament\Widgets;

use App\Models\Presence;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BureauAgentsStatsWidget extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.bureau-agents-stats-widget';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->isChefBureau() && $user->bureau_id;
    }

    protected function getViewData(): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->bureau_id) {
            return ['agents' => [], 'moisLabel' => ''];
        }

        $now = Carbon::now();
        $moisLabel = $now->translatedFormat('F Y');

        $agents = User::where('bureau_id', $user->bureau_id)
            ->whereIn('role', [User::ROLE_AGENT, User::ROLE_CHEF_BUREAU])
            ->withCount([
                'presences as presences_present_count' => fn ($q) => $q
                    ->where('statut', Presence::STATUT_PRESENT)
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year),
                'presences as presences_retard_count' => fn ($q) => $q
                    ->where('statut', Presence::STATUT_RETARD)
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year),
                'presences as presences_total_count' => fn ($q) => $q
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year),
            ])
            ->orderBy('nom')
            ->get();

        return [
            'agents' => $agents,
            'moisLabel' => $moisLabel,
        ];
    }
}
