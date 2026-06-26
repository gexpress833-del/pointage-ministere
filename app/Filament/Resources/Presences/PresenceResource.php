<?php

namespace App\Filament\Resources\Presences;

use App\Filament\Resources\Presences\Pages\ListPresences;
use App\Models\Presence;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PresenceResource extends Resource
{
    protected static ?string $model = Presence::class;

    protected static ?string $navigationLabel = 'Présence des signeurs';

    protected static ?string $modelLabel = 'Présence';

    protected static ?string $pluralModelLabel = 'Présences';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $slug = 'presences-signees';

    protected static string|\UnitEnum|null $navigationGroup = 'Présence';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('session.date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $search = trim($search);
                        $parsedDate = null;

                        foreach (['d/m/Y', 'Y-m-d'] as $format) {
                            try {
                                $candidate = Carbon::createFromFormat($format, $search);
                                if ($candidate !== false) {
                                    $parsedDate = $candidate;
                                    break;
                                }
                            } catch (\Throwable) {
                                // Ignore invalid date format and fall back to text search.
                            }
                        }

                        return $query->whereHas('session', function (Builder $sessionQuery) use ($search, $parsedDate): void {
                            if ($parsedDate) {
                                $sessionQuery->whereDate('date', $parsedDate->format('Y-m-d'));

                                return;
                            }

                            // Allows search by "YYYY-MM" or "YYYY-MM-DD" fragments.
                            $sessionQuery->where('date', 'like', '%'.$search.'%');
                        });
                    })
                    ->sortable(),
                TextColumn::make('user.nom')
                    ->label('Agent')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.matricule')
                    ->label('Matricule')
                    ->searchable(),
                TextColumn::make('user.bureau.nom_bureau')
                    ->label('Bureau')
                    ->default('—'),
                TextColumn::make('heure_arrivee')
                    ->label('Heure d\'arrivée')
                    ->sortable(),
                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $normalized = mb_strtolower(trim($search));
                        $withoutAccents = strtr($normalized, [
                            'à' => 'a', 'â' => 'a', 'ä' => 'a',
                            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
                            'î' => 'i', 'ï' => 'i',
                            'ô' => 'o', 'ö' => 'o',
                            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
                            'ç' => 'c',
                        ]);

                        $statusByKeyword = [
                            'present' => Presence::STATUT_PRESENT,
                            'retard' => Presence::STATUT_RETARD,
                            'absent' => Presence::STATUT_ABSENT,
                        ];

                        $matchedStatus = null;
                        foreach ($statusByKeyword as $keyword => $statusValue) {
                            if (str_contains($withoutAccents, $keyword) || str_contains($keyword, $withoutAccents)) {
                                $matchedStatus = $statusValue;
                                break;
                            }
                        }

                        if ($matchedStatus) {
                            return $query->where('statut', $matchedStatus);
                        }

                        return $query->where('statut', 'like', '%'.$withoutAccents.'%');
                    })
                    ->color(fn (string $state): string => match ($state) {
                        Presence::STATUT_PRESENT => 'success',
                        Presence::STATUT_RETARD => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Presence::STATUT_PRESENT => 'Présent',
                        Presence::STATUT_RETARD => 'Retard',
                        Presence::STATUT_ABSENT => 'Absent',
                        default => $state,
                    }),
            ])
            ->defaultSort('session.date', 'desc')
            ->filters([
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        Presence::STATUT_PRESENT => 'Présent',
                        Presence::STATUT_RETARD => 'Retard',
                    ]),
                Filter::make('date')
                    ->label('Date')
                    ->form([
                        DatePicker::make('date')->label('Date')->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['date'],
                            fn (Builder $q, $date) => $q->whereHas(
                                'session',
                                fn (Builder $sq) => $sq->whereDate('date', $date)
                            )
                        );
                    }),
            ])
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPresences::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user && in_array($user->role, [
            User::ROLE_ADMIN,
            User::ROLE_COORDINATEUR,
            User::ROLE_CHEF_BUREAU,
        ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(mixed $record): bool
    {
        return Auth::user()?->isAdministrateur() ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['user.bureau', 'session']);
        $user = Auth::user();

        if ($user && $user->isChefBureau() && $user->bureau_id) {
            $query->whereHas('user', fn (Builder $q) => $q->where('bureau_id', $user->bureau_id));
        }

        return $query;
    }
}
