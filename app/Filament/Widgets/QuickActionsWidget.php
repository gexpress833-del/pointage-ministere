<?php

namespace App\Filament\Widgets;

use App\Models\Presence;
use App\Models\SessionPresence;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class QuickActionsWidget extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.quick-actions-widget';

    protected static ?int $sort = 0;

    protected function getViewData(): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        $session = SessionPresence::where('date', today())
            ->where('statut', SessionPresence::STATUT_OUVERTE)
            ->first();

        $dejaSigne = $session && $user
            ? Presence::where('user_id', $user->id)->where('session_id', $session->id)->exists()
            : false;

        $canSign = $session && ! $dejaSigne;

        $canViewReports = $user && in_array($user->role, [
            User::ROLE_ADMIN,
            User::ROLE_COORDINATEUR,
            User::ROLE_CHEF_BUREAU,
        ]);

        return [
            'user' => $user,
            'session' => $session,
            'canSign' => $canSign,
            'dejaSigne' => $dejaSigne,
            'canViewReports' => $canViewReports,
        ];
    }
}
