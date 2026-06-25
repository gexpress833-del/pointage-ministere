<?php

namespace App\Http\Controllers\Auth;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilamentLogoutController
{
    public function __invoke(Request $request): LogoutResponse
    {
        // Déconnexion du guard Filament (généralement 'web')
        Filament::auth()->logout();

        // Déconnexion explicite du guard web pour invalider le cookie "remember me"
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return app(LogoutResponse::class);
    }
}
