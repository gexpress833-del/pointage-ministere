<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Vérifier que le password est présent et non vide
        if (empty($data['password'])) {
            $this->halt();
        }

        // Hasher le password
        $data['password'] = Hash::make($data['password']);

        return $data;
    }
}
