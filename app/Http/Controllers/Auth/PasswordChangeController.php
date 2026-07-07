<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
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
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        $user->password = $validated['password'];
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('home')->with('status', 'Votre mot de passe a été modifié avec succès.');
    }
}
