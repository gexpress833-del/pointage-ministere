<?php

namespace App\Filament\Resources\Presences\Pages;

use App\Filament\Resources\Presences\PresenceResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPresences extends ListRecords
{
    protected static string $resource = PresenceResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        $actions = [];

        if ($user && $user->isAdministrateur()) {
            $actions[] = Action::make('rapport_date')
                ->label('Rapport à une date')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->form([
                    DatePicker::make('date')
                        ->label('Date du rapport')
                        ->displayFormat('d/m/Y')
                        ->default(now())
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $url = route('reports.date', ['date' => $data['date']]);
                    $this->redirect($url, navigate: false);
                });

            $actions[] = Action::make('rapport_agent')
                ->label('Rapport d\'un agent')
                ->icon('heroicon-o-user')
                ->color('gray')
                ->form([
                    Select::make('user_id')
                        ->label('Agent')
                        ->options(User::whereIn('role', [User::ROLE_AGENT, User::ROLE_CHEF_BUREAU, User::ROLE_SECRETAIRE, User::ROLE_COORDINATEUR])
                            ->orderBy('nom')
                            ->pluck('nom', 'id')
                            ->toArray())
                        ->searchable()
                        ->required(),
                    DatePicker::make('start')
                        ->label('Date de début')
                        ->displayFormat('d/m/Y')
                        ->default(now()->startOfMonth()),
                    DatePicker::make('end')
                        ->label('Date de fin')
                        ->displayFormat('d/m/Y')
                        ->default(now()),
                ])
                ->action(function (array $data): void {
                    $url = route('reports.user', array_filter([
                        'user' => $data['user_id'],
                        'start' => $data['start'] ?? null,
                        'end' => $data['end'] ?? null,
                    ]));
                    $this->redirect($url, navigate: false);
                });
        }

        return $actions;
    }
}
