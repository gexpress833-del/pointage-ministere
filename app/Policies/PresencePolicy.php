<?php

namespace App\Policies;

use App\Models\Presence;
use App\Models\User;

class PresencePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrateur() || $user->isCoordinateur() || $user->isChefBureau() || $user->isAgent();
    }

    public function view(User $user, Presence $presence): bool
    {
        if ($user->isAdministrateur() || $user->isCoordinateur()) {
            return true;
        }
        if ($user->isChefBureau()) {
            return $presence->user->bureau_id === $user->bureau_id;
        }

        return $presence->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true; // signature gérée par PresenceController
    }

    public function update(User $user, Presence $presence): bool
    {
        return $user->isAdministrateur();
    }

    public function delete(User $user, Presence $presence): bool
    {
        return $user->isAdministrateur();
    }
}
