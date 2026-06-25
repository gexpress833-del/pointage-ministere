<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Les valeurs &lt; 0,35 correspondaient souvent à une confusion « 15 % » alors que face-api
     * utilise une distance euclidienne (souvent 0,35–0,55 pour la même personne).
     */
    public function up(): void
    {
        $rows = DB::table('parametres')
            ->where('cle', 'seuil_reconnaissance_faciale')
            ->get();

        foreach ($rows as $row) {
            $v = (float) $row->valeur;
            if ($v > 0 && $v < 0.35) {
                DB::table('parametres')->where('id', $row->id)->update([
                    'valeur' => '0.55',
                    'description' => 'Distance euclidienne maximale acceptée (face-api, typ. 0,45–0,60). Recommandé : 0,55.',
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
