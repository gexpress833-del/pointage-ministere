<?php

namespace App\Http\Controllers\Auth;

use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilamentLogoutController
{
    public function __invoke(Request $request)
    {
        // Déconnexion du guard Filament (généralement 'web')
        Filament::auth()->logout();

        // Déconnexion explicite du guard web pour invalider le cookie "remember me"
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
