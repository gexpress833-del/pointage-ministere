<?php

namespace App\Filament\Resources\Annonces\Pages;

use App\Filament\Resources\Annonces\AnnonceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnonce extends CreateRecord
{
    protected static string $resource = AnnonceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
