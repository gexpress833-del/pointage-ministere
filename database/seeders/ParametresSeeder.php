<?php

namespace Database\Seeders;

use App\Models\Parametre;
use Illuminate\Database\Seeder;

class ParametresSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'cle' => Parametre::CLE_HEURE_LIMITE_RETARD,
                'valeur' => '08:00',
                'type' => 'time',
                'description' => 'Heure limite pour considérer un retard (format HH:MM)',
            ],
            [
                'cle' => Parametre::CLE_HEURE_REFERENCE_DEPART,
                'valeur' => '17:00',
                'type' => 'time',
                'description' => 'Heure de référence pour le départ (fin de journée attendue, HH:MM). Indicatif pour les rapports ; le départ réel reste le pointage de l\'agent.',
            ],
            [
                'cle' => Parametre::CLE_SEUIL_RECONNAISSANCE,
                'valeur' => '0.55',
                'type' => 'string',
                'description' => 'Distance euclidienne max. (face-api.js, pas un pourcentage). Recommandé 0,45–0,60 ; 0,55 = équilibré.',
            ],
        ];

        foreach ($defaults as $row) {
            Parametre::firstOrCreate(
                ['cle' => $row['cle']],
                $row
            );
        }
    }
}
