<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\ChefBureauPanelProvider;
use App\Providers\Filament\CoordinateurPanelProvider;
use App\Providers\Filament\SecretairePanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    SecretairePanelProvider::class,
    CoordinateurPanelProvider::class,
    ChefBureauPanelProvider::class,
];
