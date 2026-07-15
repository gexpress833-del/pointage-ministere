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
            [
                'cle' => Parametre::CLE_HEURE_OUVERTURE_SESSION,
                'valeur' => '07:59',
                'type' => 'time',
                'description' => 'Heure d\'ouverture automatique de la session de présence (HH:MM).',
            ],
            [
                'cle' => Parametre::CLE_HEURE_FIN_ARRIVEE,
                'valeur' => '11:59',
                'type' => 'time',
                'description' => 'Heure limite pour pointer l\'arrivée (HH:MM).',
            ],
            [
                'cle' => Parametre::CLE_HEURE_DEBUT_DEPART,
                'valeur' => '15:59',
                'type' => 'time',
                'description' => 'Heure de début du pointage de départ (HH:MM).',
            ],
            [
                'cle' => Parametre::CLE_HEURE_FIN_DEPART_NORMAL,
                'valeur' => '16:59',
                'type' => 'time',
                'description' => 'Heure de fin du départ normal ; au-delà = heures supplémentaires (HH:MM).',
            ],
            [
                'cle' => Parametre::CLE_HEURE_FERMETURE_SESSION,
                'valeur' => '23:59',
                'type' => 'time',
                'description' => 'Heure de fermeture automatique de la session (HH:MM).',
            ],
            [
                'cle' => Parametre::CLE_SESSION_AUTO_OPEN,
                'valeur' => '1',
                'type' => 'boolean',
                'description' => 'Activer l\'ouverture automatique des sessions par le planificateur.',
            ],
            [
                'cle' => Parametre::CLE_SESSION_AUTO_CLOSE,
                'valeur' => '1',
                'type' => 'boolean',
                'description' => 'Activer la fermeture automatique des sessions à l\'heure configurée.',
            ],
            [
                'cle' => Parametre::CLE_SESSION_ALLOW_REOPEN,
                'valeur' => '1',
                'type' => 'boolean',
                'description' => 'Autoriser la réouverture manuelle d\'une session fermée par l\'admin.',
            ],
            [
                'cle' => Parametre::CLE_SESSION_ALLOW_RESET_PRESENCES,
                'valeur' => '1',
                'type' => 'boolean',
                'description' => 'Autoriser la réinitialisation (effacement) des pointages sur une période donnée.',
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
