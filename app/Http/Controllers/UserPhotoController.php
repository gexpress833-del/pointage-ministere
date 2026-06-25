<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserPhotoController extends Controller
{
    public function show(Request $request, User $user)
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        abort_unless($currentUser, 403);

        $canView = $currentUser->isAdministrateur()
            || $currentUser->isCoordinateur()
            || ($currentUser->id === $user->id)
            || (
                $currentUser->isChefBureau()
                && $currentUser->bureau_id
                && $currentUser->bureau_id === $user->bureau_id
            );

        abort_unless($canView, 403);

        if (! $user->photo_reference || ! Storage::disk('local')->exists($user->photo_reference)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($user->photo_reference));
    }
}
