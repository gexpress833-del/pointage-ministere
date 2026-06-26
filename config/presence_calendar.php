<?php

/**
 * Calendrier pour les rapports de présence (Kolwezi / RDC).
 *
 * Jours fériés récurrents : clés au format « mm-jj » (année ignorée).
 * Compléter par « extra » pour des dates ponctuelles (ex. lundi de Pâques) : « Y-m-d » => libellé.
 */
return [

    'recurring_holidays' => [
        '01-01' => 'Nouvel An',
        '01-04' => 'Martyrs de l\'indépendance',
        '05-01' => 'Fête du Travail',
        '05-17' => 'Journée de la Libération',
        '06-30' => 'Indépendance',
        '08-01' => 'Fête des parents',
        '11-17' => 'Journée des Forces armées',
        '12-25' => 'Noël',
    ],

    /*
    |--------------------------------------------------------------------------
    | Jours fériés variables ou exceptionnels (clé Y-m-d)
    |--------------------------------------------------------------------------
    */
    'extra_holidays' => [
        // Exemples (à adapter selon le calendrier officiel année par année) :
        // '2026-04-06' => 'Lundi de Pâques',
    ],

];
