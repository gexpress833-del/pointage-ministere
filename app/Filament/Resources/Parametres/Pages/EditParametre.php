<?php

namespace App\Filament\Resources\Parametres\Pages;

use App\Filament\Resources\Parametres\ParametreResource;
use Carbon\CarbonInterface;
use Filament\Resources\Pages\EditRecord;

class EditParametre extends EditRecord
{
    protected static string $resource = ParametreResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['type']) && $data['type'] === 'time' && isset($data['valeur'])) {
            $v = $data['valeur'];
            $data['valeur'] = $v instanceof CarbonInterface ? $v->format('H:i') : (string) $v;
        }

        return $data;
    }
}
