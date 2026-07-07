<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    public function showChangeForm(): View
    {
        return view('auth.password-change');
    }

    public function change(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Le mot de passe actuel est incorrect.',
            ]);
        }

        if (Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Le nouveau mot de passe doit être différent de l\'ancien.',
            ]);
        }

        $user->password = $validated['password'];
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('home')->with('status', 'Votre mot de passe a été modifié avec succès.');
    }
}
