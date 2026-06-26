<?php

namespace App\Policies;

use App\Models\Annonce;
use App\Models\User;

class AnnoncePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrateur();
    }

    public function view(User $user, Annonce $annonce): bool
    {
        return $user->isAdministrateur();
    }

    public function create(User $user): bool
    {
        return $user->isAdministrateur();
    }

    public function update(User $user, Annonce $annonce): bool
    {
        return $user->isAdministrateur();
    }

    public function delete(User $user, Annonce $annonce): bool
    {
        return $user->isAdministrateur();
    }
}
