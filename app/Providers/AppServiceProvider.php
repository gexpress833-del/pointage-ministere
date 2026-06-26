<?php

namespace App\Providers;

use App\Http\Controllers\Auth\FilamentLogoutController;
use App\Http\Responses\LoginResponse;
use Filament\Auth\Http\Controllers\LogoutController;
use App\Models\Annonce;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            LogoutController::class,
            FilamentLogoutController::class
        );

        $this->app->bind(
            \Filament\Auth\Http\Responses\Contracts\LoginResponse::class,
            LoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Évite l'erreur "SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long"
        // avec MySQL et utf8mb4 (limite d'index ~767 bytes = 191 caractères en utf8mb4).
        Schema::defaultStringLength(191);

        View::composer(
            [
                'presence.dashboard',
                'presence.historique',
                'presence.sign',
                'presence.sign-blocked',
            ],
            function ($view): void {
                if (! Schema::hasTable('annonces')) {
                    $view->with('annoncesActives', collect());

                    return;
                }
                $view->with(
                    'annoncesActives',
                    Annonce::query()->publique()->orderByDesc('published_at')->limit(5)->get()
                );
            }
        );
    }
}
