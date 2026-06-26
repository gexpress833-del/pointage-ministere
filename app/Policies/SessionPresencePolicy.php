<?php

namespace App\Policies;

use App\Models\SessionPresence;
use App\Models\User;

class SessionPresencePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrateur() || $user->isCoordinateur() || $user->isChefBureau() || $user->isAgent();
    }

    public function view(User $user, SessionPresence $session): bool
    {
        if ($user->isAdministrateur() || $user->isCoordinateur()) {
            return true;
        }
        if ($user->isChefBureau()) {
            return true; // peut voir toutes les sessions, les presences seront filtrées par bureau si besoin
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdministrateur();
    }

    public function update(User $user, SessionPresence $session): bool
    {
        return $user->isAdministrateur();
    }

    public function delete(User $user, SessionPresence $session): bool
    {
        return $user->isAdministrateur();
    }
}
