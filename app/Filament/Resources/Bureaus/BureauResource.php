<?php

namespace App\Filament\Resources\Bureaus;

use App\Filament\Resources\Bureaus\Pages\CreateBureau;
use App\Filament\Resources\Bureaus\Pages\EditBureau;
use App\Filament\Resources\Bureaus\Pages\ListBureaus;
use App\Filament\Resources\Bureaus\Schemas\BureauForm;
use App\Filament\Resources\Bureaus\Tables\BureausTable;
use App\Models\Bureau;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BureauResource extends Resource
{
    protected static ?string $model = Bureau::class;

    protected static ?string $navigationLabel = 'Bureaux';

    protected static ?string $modelLabel = 'Bureau';

    protected static ?string $pluralModelLabel = 'Bureaux';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Organisation';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdministrateur() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return BureauForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BureausTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBureaus::route('/'),
            'create' => CreateBureau::route('/create'),
            'edit' => EditBureau::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        if ($user && $user->isChefBureau() && $user->bureau_id) {
            $query->where('id', $user->bureau_id);
        }

        return $query;
    }
}
