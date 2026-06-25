<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePortalProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::user();
        $user->load(['bureau', 'service']);

        return view('presence.profile', [
            'user' => $user,
        ]);
    }

    public function update(UpdatePortalProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $user->update($request->validated());

        return redirect()
            ->route('presence.profile')
            ->with('status', 'Vos coordonnées ont été mises à jour.');
    }
}
