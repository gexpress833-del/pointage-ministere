<?php

namespace App\Filament\Resources\Bureaus\Pages;

use App\Filament\Resources\Bureaus\BureauResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBureau extends EditRecord
{
    protected static string $resource = BureauResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
