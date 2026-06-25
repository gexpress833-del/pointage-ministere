<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mettre à jour le seuil de reconnaissance faciale à 0.6 (très facile et permissif)
        DB::table('parametres')->updateOrInsert(
            ['cle' => 'seuil_reconnaissance_faciale'],
            [
                'valeur' => '0.6',
                'type' => 'string',
                'description' => 'Seuil de similarité pour la reconnaissance faciale (0-1). 0.6 = reconnaissance très facile et permissive.',
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à la valeur par défaut
        DB::table('parametres')
            ->where('cle', 'seuil_reconnaissance_faciale')
            ->update(['valeur' => '0.6']);
    }
};
