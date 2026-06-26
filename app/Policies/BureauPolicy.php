<?php

namespace App\Policies;

use App\Models\Bureau;
use App\Models\User;

class BureauPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrateur() || $user->isCoordinateur() || $user->isChefBureau();
    }

    public function view(User $user, Bureau $bureau): bool
    {
        if ($user->isAdministrateur() || $user->isCoordinateur()) {
            return true;
        }

        return $user->isChefBureau() && $user->bureau_id === $bureau->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdministrateur();
    }

    public function update(User $user, Bureau $bureau): bool
    {
        return $user->isAdministrateur();
    }

    public function delete(User $user, Bureau $bureau): bool
    {
        return $user->isAdministrateur();
    }
}
