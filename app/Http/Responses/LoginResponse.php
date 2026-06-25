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

        // Les agents et chefs de bureau vont vers leur portail personnel
        if ($user && in_array($user->role, [User::ROLE_AGENT, User::ROLE_CHEF_BUREAU])) {
            return redirect()->route('presence.dashboard');
        }

        // Admins et coordinateurs vont vers le tableau de bord Filament
        return redirect()->to(filament()->getHomeUrl() ?? '/admin');
    }
}
