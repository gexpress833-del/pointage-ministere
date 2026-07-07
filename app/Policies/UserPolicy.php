<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrateur() || $user->isSecretaire() || $user->isChefBureau();
    }

    public function view(User $user, User $model): bool
    {
        if ($user->isAdministrateur() || $user->isSecretaire()) {
            return true;
        }
        if ($user->isChefBureau() && $user->bureau_id) {
            return $model->bureau_id === $user->bureau_id;
        }

        return $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdministrateur() || $user->isSecretaire();
    }

    public function update(User $user, User $model): bool
    {
        if ($user->isAdministrateur() || $user->isSecretaire()) {
            return true;
        }
        if ($user->isChefBureau() && $user->bureau_id) {
            return $model->bureau_id === $user->bureau_id;
        }

        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isAdministrateur();
    }
}
