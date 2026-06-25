<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Évite les 500 par timeout (ex. grosses tables MySQL + Livewire) sur le panneau Filament.
 */
class ExtendExecutionTimeForFilament
{
    public function handle(Request $request, Closure $next): Response
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(120);
        }

        return $next($request);
    }
}
