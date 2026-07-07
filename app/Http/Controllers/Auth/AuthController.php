<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $login = $request->input('login');
        $password = $request->input('password');

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'telephone';

        if (! Auth::attempt([$field => $login, 'password' => $password], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => __('Les identifiants fournis ne correspondent pas à nos enregistrements.'),
            ]);
        }

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function redirectByRole(User $user)
    {
        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        if ($user->isAgent()) {
            return redirect()->route('presence.dashboard');
        }

        $panelPath = match ($user->role) {
            User::ROLE_ADMIN => '/admin',
            User::ROLE_SECRETAIRE => '/secretaire',
            User::ROLE_COORDINATEUR => '/coordinateur',
            User::ROLE_CHEF_BUREAU => '/chef',
            default => '/',
        };

        return redirect()->to($panelPath);
    }
}
