<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\AnnoncesWidget;
use App\Filament\Widgets\BureauAgentsStatsWidget;
use App\Filament\Widgets\PresenceStatsWidget;
use App\Filament\Widgets\QuickActionsWidget;
use App\Http\Middleware\ExtendExecutionTimeForFilament;
use App\Http\Middleware\RedirectAgentFromPanel;
use App\Models\User;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CoordinateurPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('coordinateur')
            ->path('coordinateur')
            ->login()
            ->brandName(config('app.name'))
            ->brandLogo(fn () => '<img src="'.asset('logo3.png').'" alt="Logo" class="h-8 w-auto object-contain">')
            ->darkModeBrandLogo(fn () => '<img src="'.asset('logo3.png').'" alt="Logo" class="h-8 w-auto object-contain">')
            ->colors([
                'primary' => Color::Green,
            ])
            ->darkMode(true)
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.hooks.panel-theme')->render(),
            )
            ->navigationGroups([
                'Présence',
                'Organisation',
                'Communication',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                QuickActionsWidget::class,
                AnnoncesWidget::class,
                PresenceStatsWidget::class,
            ])
            ->navigationItems([
                NavigationItem::make('Accueil du site')
                    ->url(url('/'))
                    ->icon(Heroicon::OutlinedArrowLeftStartOnRectangle)
                    ->group('Présence')
                    ->sort(0)
                    ->visible(fn () => Auth::user()?->role === User::ROLE_COORDINATEUR),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                ExtendExecutionTimeForFilament::class,
                RedirectAgentFromPanel::class,
                \App\Http\Middleware\ForcePasswordChange::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
