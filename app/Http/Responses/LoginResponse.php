<?php

namespace App\Http\Responses;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): mixed
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()->to('/');
        }

        // Forcer le changement de mot de passe à la première connexion
        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        // Les agents vont vers leur portail personnel
        if ($user->isAgent()) {
            return redirect()->route('presence.dashboard');
        }

        // Chaque rôle va vers son propre panel Filament
        $panelPath = match ($user->role) {
            User::ROLE_ADMIN => '/admin',
            User::ROLE_SECRETAIRE => '/secretaire',
            User::ROLE_COORDINATEUR => '/coordinateur',
            User::ROLE_CHEF_BUREAU => '/chef',
            default => '/',
        };

        return redirect()->to($panelPath);
    }
}
