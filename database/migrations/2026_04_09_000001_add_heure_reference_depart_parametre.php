<?php

use App\Models\Parametre;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Parametre::query()->firstOrCreate(
            ['cle' => Parametre::CLE_HEURE_REFERENCE_DEPART],
            [
                'valeur' => '17:00',
                'type' => 'time',
                'description' => 'Heure de référence pour le départ (fin de journée attendue, HH:MM). Indicatif pour les rapports ; le départ réel reste le pointage de l\'agent.',
            ]
        );
    }

    public function down(): void
    {
        Parametre::query()->where('cle', Parametre::CLE_HEURE_REFERENCE_DEPART)->delete();
    }
};
