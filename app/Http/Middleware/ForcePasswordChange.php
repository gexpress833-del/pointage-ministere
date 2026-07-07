<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return $next($request);
        }

        // Laisser passer les routes de changement de mot de passe et de déconnexion
        if ($request->routeIs('password.change') || $request->routeIs('password.change.submit') || $request->routeIs('logout')) {
            return $next($request);
        }

        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
