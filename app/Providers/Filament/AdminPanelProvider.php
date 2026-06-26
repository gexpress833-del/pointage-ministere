<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\AnnoncesWidget;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName(config('app.name'))
            ->colors([
                'primary' => Color::Blue,
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
                'Configuration',
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
                NavigationItem::make('Tableau de bord')
                    ->url(url('/presence'))
                    ->icon(Heroicon::OutlinedHome)
                    ->group('Présence')
                    ->sort(0)
                    ->visible(fn () => in_array(Auth::user()?->role, [
                        User::ROLE_AGENT,
                        User::ROLE_CHEF_BUREAU,
                    ])),
                NavigationItem::make('Signer présence')
                    ->url(url('/presence/sign'))
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->group('Présence')
                    ->sort(1)
                    ->visible(fn () => in_array(Auth::user()?->role, [
                        User::ROLE_AGENT,
                        User::ROLE_CHEF_BUREAU,
                    ])),
                NavigationItem::make('Historique')
                    ->url(url('/presence/historique'))
                    ->icon(Heroicon::OutlinedClock)
                    ->group('Présence')
                    ->sort(2)
                    ->visible(fn () => in_array(Auth::user()?->role, [
                        User::ROLE_AGENT,
                        User::ROLE_CHEF_BUREAU,
                    ])),
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
