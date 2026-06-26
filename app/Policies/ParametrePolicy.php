<?php

namespace App\Policies;

use App\Models\Parametre;
use App\Models\User;

class ParametrePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrateur();
    }

    public function view(User $user, Parametre $parametre): bool
    {
        return $user->isAdministrateur();
    }

    public function create(User $user): bool
    {
        return $user->isAdministrateur();
    }

    public function update(User $user, Parametre $parametre): bool
    {
        return $user->isAdministrateur();
    }

    public function delete(User $user, Parametre $parametre): bool
    {
        return $user->isAdministrateur();
    }
}
