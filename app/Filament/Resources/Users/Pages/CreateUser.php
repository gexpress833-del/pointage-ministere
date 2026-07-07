<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\ImagekitService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $tempPassword = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->tempPassword = \Illuminate\Support\Str::random(12);
        $data['password'] = $this->tempPassword;
        $data['must_change_password'] = true;

        // Upload direct de la photo vers Imagekit
        if (! empty($data['photo_reference']) && Storage::disk('local')->exists($data['photo_reference'])) {
            $localPath = $data['photo_reference'];
            try {
                $imagekit = app(ImagekitService::class);
                $localFile = Storage::disk('local')->path($localPath);
                $extension = pathinfo($localFile, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = 'ref_'.time().'.'.$extension;

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

    protected function afterCreate(): void
    {
        if ($this->tempPassword) {
            \Filament\Notifications\Notification::make()
                ->title('Utilisateur créé avec succès')
                ->body("Mot de passe temporaire : {$this->tempPassword} — L'utilisateur devra le changer à sa première connexion.")
                ->success()
                ->persistent()
                ->send();
        } else {
            \Filament\Notifications\Notification::make()
                ->title('Utilisateur créé avec succès')
                ->body('L\'utilisateur a été ajouté à la liste.')
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('create')
                ->label('Créer l\'utilisateur')
                ->submit('create')
                ->keyBindings(['cmd+s', 'ctrl+s']),
            \Filament\Actions\Action::make('cancel')
                ->label('Annuler')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
