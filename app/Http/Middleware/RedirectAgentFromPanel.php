<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAgentFromPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = auth()->user();

        // Ne pas intercepter les routes d'authentification (login/logout) d'aucun panel
        if ($request->routeIs('filament.*.auth.*')) {
            return $next($request);
        }

        if (! $user) {
            return $next($request);
        }

        $currentPanel = \Filament\Facades\Filament::getCurrentPanel();
        $currentPanelId = $currentPanel?->getId();

        $ownPanelId = match ($user->role) {
            User::ROLE_ADMIN => 'admin',
            User::ROLE_SECRETAIRE => 'secretaire',
            User::ROLE_COORDINATEUR => 'coordinateur',
            User::ROLE_CHEF_BUREAU => 'chef',
            default => null,
        };

        $ownPanelPath = match ($user->role) {
            User::ROLE_ADMIN => '/admin',
            User::ROLE_SECRETAIRE => '/secretaire',
            User::ROLE_COORDINATEUR => '/coordinateur',
            User::ROLE_CHEF_BUREAU => '/chef',
            User::ROLE_AGENT => route('presence.dashboard'),
            default => null,
        };

        // Si l'utilisateur est un agent, ou navigue sur un panel qui n'est pas le sien, on le redirige.
        if ($user->role === User::ROLE_AGENT || ($ownPanelId !== null && $currentPanelId !== $ownPanelId)) {
            return $ownPanelPath ? redirect()->to($ownPanelPath) : $next($request);
        }

        return $next($request);
    }
}
