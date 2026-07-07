<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImagekitService;
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
            || $currentUser->isSecretaire()
            || $currentUser->isCoordinateur()
            || ($currentUser->id === $user->id)
            || (
                $currentUser->isChefBureau()
                && $currentUser->bureau_id
                && $currentUser->bureau_id === $user->bureau_id
            );

        abort_unless($canView, 403);

        if (! $user->photo_reference) {
            abort(404);
        }

        // Si c'est une URL Imagekit, proxifier l'image
        if (ImagekitService::isImagekitUrl($user->photo_reference)) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(15)->get($user->photo_reference);
                if ($response->successful()) {
                    return response($response->body(), 200, [
                        'Content-Type' => $response->header('Content-Type', 'image/jpeg'),
                        'Cache-Control' => 'public, max-age=3600',
                    ]);
                }
            } catch (\Exception $e) {
                abort(404);
            }
        }

        // Fallback: fichier local (anciennes photos pas encore migrées)
        if (! Storage::disk('local')->exists($user->photo_reference)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($user->photo_reference));
    }
}
