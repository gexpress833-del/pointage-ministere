<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DownloadFaceApiWeights extends Command
{
    protected $signature = 'pointage:download-face-weights';

    protected $description = 'Télécharge les modèles (weights) face-api.js dans public/models/';

    private const BASE = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/';

    private const FILES = [
        'tiny_face_detector_model-weights_manifest.json',
        'tiny_face_detector_model-shard1',
        'face_landmark_68_model-weights_manifest.json',
        'face_landmark_68_model-shard1',
        'face_recognition_model-weights_manifest.json',
        'face_recognition_model-shard1',
        'face_recognition_model-shard2',
    ];

    public function handle(): int
    {
        $dir = public_path('models');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        foreach (self::FILES as $file) {
            $url = self::BASE.$file;
            $path = $dir.DIRECTORY_SEPARATOR.$file;
            $this->info("Téléchargement de {$file}...");
            try {
                $response = Http::timeout(120)->get($url);
                if (! $response->successful()) {
                    $this->error("Échec: {$file}");

                    continue;
                }
                file_put_contents($path, $response->body());
                $this->line('  OK');
            } catch (\Throwable $e) {
                $this->error('  Erreur: '.$e->getMessage());
            }
        }

        $this->info('Terminé. Les modèles sont dans public/models/');

        return self::SUCCESS;
    }
}
