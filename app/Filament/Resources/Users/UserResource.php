<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Utilisateurs';

    protected static ?string $modelLabel = 'Utilisateur';

    protected static ?string $pluralModelLabel = 'Utilisateurs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Organisation';

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user && in_array($user->role, [
            User::ROLE_ADMIN,
            User::ROLE_SECRETAIRE,
            User::ROLE_CHEF_BUREAU,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user && $user->isChefBureau() && $user->bureau_id) {
            $query->where('bureau_id', $user->bureau_id)
                  ->where('role', User::ROLE_AGENT);
        }

        return $query->withCount([
            'presences as presences_present_count' => fn (Builder $q) => $q->where('statut', \App\Models\Presence::STATUT_PRESENT),
            'presences as presences_retard_count' => fn (Builder $q) => $q->where('statut', \App\Models\Presence::STATUT_RETARD),
            'presences as presences_total_count',
        ]);
    }
}
