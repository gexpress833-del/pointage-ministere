<?php

namespace App\Filament\Widgets;

use App\Models\Annonce;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Schema;

class AnnoncesWidget extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.annonces-widget';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        if (! Schema::hasTable('annonces')) {
            return ['annonces' => collect()];
        }

        return [
            'annonces' => Annonce::query()->publique()->orderByDesc('published_at')->limit(5)->get(),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->isAdministrateur() || $user->isCoordinateur() || $user->isChefBureau());
    }
}
