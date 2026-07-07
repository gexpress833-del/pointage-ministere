<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\ImagekitService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        if ($user && ($user->isChefBureau() || $user->isSecretaire())) {
            return [];
        }

        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ne jamais modifier le mot de passe depuis le panel
        unset($data['password']);

        // Upload direct de la photo vers Imagekit si une nouvelle photo a été uploadée
        if (! empty($data['photo_reference']) && ! ImagekitService::isImagekitUrl($data['photo_reference']) && Storage::disk('local')->exists($data['photo_reference'])) {
            $localPath = $data['photo_reference'];
            try {
                $imagekit = app(ImagekitService::class);
                $localFile = Storage::disk('local')->path($localPath);
                $extension = pathinfo($localFile, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = 'ref_'.$this->record->id.'_'.time().'.'.$extension;

                $result = $imagekit->upload($localFile, $fileName, '/photos_reference');
                $data['photo_reference'] = $result['url'];

                Storage::disk('local')->delete($localPath);
            } catch (\Exception $e) {
                \Filament\Notifications\Notification::make()
                    ->title('Erreur Imagekit')
                    ->body('La photo n\'a pas pu être uploadée: '.$e->getMessage())
                    ->danger()
                    ->persistent()
                    ->send();
            }
        }

        return $data;
    }
}
