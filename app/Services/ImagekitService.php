<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ImagekitService
{
    private string $privateKey;

    private string $publicKey;

    private string $urlEndpoint;

    private string $uploadEndpoint;

    private string $managementEndpoint;

    public function __construct()
    {
        $this->privateKey = config('imagekit.private_key');
        $this->publicKey = config('imagekit.public_key');
        $this->urlEndpoint = rtrim(config('imagekit.url_endpoint'), '/');
        $this->uploadEndpoint = config('imagekit.upload_endpoint');
        $this->managementEndpoint = config('imagekit.management_endpoint');
    }

    /**
     * Upload a file (base64 or file path) to Imagekit.
     *
     * @param  string  $filePath  Full path to the file on local disk, or base64 string.
     * @param  string  $fileName  Desired file name (without extension).
     * @param  string  $folder  Optional folder path in Imagekit.
     * @return array{url: string, fileId: string, name: string}
     *
     * @throws RuntimeException
     */
    public function upload(string $filePath, string $fileName, string $folder = '/'): array
    {
        try {
            $response = Http::withBasicAuth($this->privateKey, '')
                ->timeout(60)
                ->attach('file', fopen($filePath, 'r'), $fileName)
                ->post($this->uploadEndpoint, [
                    'fileName' => $fileName,
                    'folder' => $folder,
                ]);
        } catch (ConnectionException $e) {
            Log::error('Imagekit upload connection error: '.$e->getMessage());
            throw new RuntimeException('Erreur de connexion à Imagekit: '.$e->getMessage());
        }

        if (! $response->successful()) {
            Log::error('Imagekit upload failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Imagekit upload échoué: '.$response->body());
        }

        $data = $response->json();

        return [
            'url' => $data['url'] ?? '',
            'fileId' => $data['fileId'] ?? '',
            'name' => $data['name'] ?? $fileName,
        ];
    }

    /**
     * Upload a base64-encoded image to Imagekit.
     *
     * @param  string  $base64  Raw base64 data (without data URI prefix).
     * @param  string  $fileName  Desired file name.
     * @param  string  $folder  Optional folder path.
     * @return array{url: string, fileId: string, name: string}
     */
    public function uploadBase64(string $base64, string $fileName, string $folder = '/'): array
    {
        try {
            $response = Http::withBasicAuth($this->privateKey, '')
                ->timeout(60)
                ->asMultipart()
                ->attach('file', $base64, $fileName)
                ->post($this->uploadEndpoint, [
                    'fileName' => $fileName,
                    'folder' => $folder,
                ]);
        } catch (ConnectionException $e) {
            Log::error('Imagekit upload base64 connection error: '.$e->getMessage());
            throw new RuntimeException('Erreur de connexion à Imagekit: '.$e->getMessage());
        }

        if (! $response->successful()) {
            Log::error('Imagekit base64 upload failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Imagekit upload échoué: '.$response->body());
        }

        $data = $response->json();

        return [
            'url' => $data['url'] ?? '',
            'fileId' => $data['fileId'] ?? '',
            'name' => $data['name'] ?? $fileName,
        ];
    }

    /**
     * Delete a file from Imagekit by fileId.
     */
    public function delete(string $fileId): bool
    {
        try {
            $response = Http::withBasicAuth($this->privateKey, '')
                ->timeout(30)
                ->delete("{$this->managementEndpoint}/{$fileId}");
        } catch (ConnectionException $e) {
            Log::error('Imagekit delete connection error: '.$e->getMessage());

            return false;
        }

        if (! $response->successful()) {
            Log::error('Imagekit delete failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * Build a transformation URL for an Imagekit file.
     */
    public function url(string $path, array $transformation = []): string
    {
        $path = ltrim($path, '/');

        if (empty($transformation)) {
            return "{$this->urlEndpoint}/{$path}";
        }

        $params = [];
        foreach ($transformation as $key => $value) {
            $params[] = "{$key}-{$value}";
        }

        $tr = implode(',', $params);

        return "{$this->urlEndpoint}/tr:{$tr}/{$path}";
    }

    /**
     * Check if a string looks like an Imagekit URL.
     */
    public static function isImagekitUrl(string $value): bool
    {
        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }
}
