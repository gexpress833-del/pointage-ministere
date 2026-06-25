<?php

namespace App\Filament\Resources\Bureaus\Pages;

use App\Filament\Resources\Bureaus\BureauResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBureaus extends ListRecords
{
    protected static string $resource = BureauResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
