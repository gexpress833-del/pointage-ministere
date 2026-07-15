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
            DeleteAction::make()
                ->hidden(fn () => $this->record->isProtectedAdmin())
                ->successNotification(
                    \Filament\Notifications\Notification::make()
                        ->title('Utilisateur supprimé')
                        ->body('L\'utilisateur a été supprimé avec succès.')
                        ->success()
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ne jamais modifier le mot de passe depuis le panel
        unset($data['password']);

        $oldPhoto = $this->record->photo_reference;
        $newPhoto = $data['photo_reference'] ?? null;

        // Si aucune nouvelle photo n'est fournie, conserver l'ancienne (même si l'éditeur l'a vidé temporairement)
        if (empty($newPhoto)) {
            $data['photo_reference'] = $oldPhoto;

            return $data;
        }

        // Si la valeur est déjà une URL distante (Imagekit), ne rien faire
        if (ImagekitService::isImagekitUrl($newPhoto)) {
            return $data;
        }

        // Upload de la nouvelle photo locale vers Imagekit
        if (Storage::disk('local')->exists($newPhoto)) {
            $localPath = $newPhoto;
            try {
                $imagekit = app(ImagekitService::class);
                $localFile = Storage::disk('local')->path($localPath);
                $extension = pathinfo($localFile, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = 'ref_'.$this->record->id.'_'.time().'.'.$extension;

                $result = $imagekit->upload($localFile, $fileName, '/photos_reference');
                $data['photo_reference'] = $result['url'];

                Storage::disk('local')->delete($localPath);

                // Supprimer l'ancienne photo distante si elle était hébergée sur Imagekit
                if ($oldPhoto && ImagekitService::isImagekitUrl($oldPhoto)) {
                    $this->deleteOldImagekitFile($oldPhoto);
                }
            } catch (\Exception $e) {
                \Filament\Notifications\Notification::make()
                    ->title('Erreur Imagekit')
                    ->body('La photo n\'a pas pu être uploadée: '.$e->getMessage())
                    ->danger()
                    ->persistent()
                    ->send();

                // En cas d'échec, conserver l'ancienne photo
                $data['photo_reference'] = $oldPhoto;
            }
        }

        return $data;
    }

    /**
     * Tente de supprimer l'ancien fichier sur Imagekit (récupère fileId depuis l'URL si possible).
     */
    private function deleteOldImagekitFile(string $url): void
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        $fileName = basename($path);

        if (! $fileName) {
            return;
        }

        try {
            $imagekit = app(ImagekitService::class);
            $endpoint = config('imagekit.url_endpoint');
            $folder = '/photos_reference';

            $response = \Illuminate\Support\Facades\Http::withBasicAuth(config('imagekit.private_key'), '')
                ->timeout(30)
                ->get(config('imagekit.management_endpoint').'/files', [
                    'searchQuery' => "name='{$fileName}' path='{$folder}'",
                    'limit' => 1,
                ]);

            if ($response->successful()) {
                $files = $response->json('files');
                $fileId = $files[0]['fileId'] ?? null;
                if ($fileId) {
                    $imagekit->delete($fileId);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Impossible de supprimer l\'ancienne photo Imagekit : '.$e->getMessage());
        }
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->title('Utilisateur modifié avec succès')
            ->body('Les modifications ont été enregistrées.')
            ->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
