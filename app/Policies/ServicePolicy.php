<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrateur() || $user->isCoordinateur() || $user->isChefBureau();
    }

    public function view(User $user, Service $service): bool
    {
        if ($user->isAdministrateur() || $user->isCoordinateur()) {
            return true;
        }

        return $user->isChefBureau() && $user->bureau_id === $service->bureau_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdministrateur();
    }

    public function update(User $user, Service $service): bool
    {
        return $user->isAdministrateur();
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->isAdministrateur();
    }
}
