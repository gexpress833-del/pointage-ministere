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

        // Ne pas intercepter les routes de déconnexion et de login
        if ($request->routeIs('filament.admin.auth.*')) {
            return $next($request);
        }

        if ($user && $user->role === User::ROLE_AGENT) {
            return redirect()->route('presence.dashboard');
        }

        return $next($request);
    }
}
