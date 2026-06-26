<?php

/**
 * En-tête institutionnel commun aux documents PDF et à la page d'accueil.
 *
 * Images optionnelles (chemins relatifs à /public) :
 *   public/images/official/logo-left.png   — emblème circulaire gauche
 *   public/images/official/armoiries-rdc.png — armoiries RDC droite
 *
 * Ou via .env : DOCUMENT_LOGO_LEFT, DOCUMENT_LOGO_RIGHT (chemins relatifs à public/)
 */
return [

    'logo_left' => env('DOCUMENT_LOGO_LEFT', 'images/official/logo-left.png'),

    'logo_right' => env('DOCUMENT_LOGO_RIGHT', 'images/official/armoiries-rdc.png'),

    'line1' => env('DOCUMENT_HEADER_L1', 'République Démocratique du Congo'),

    'line2' => env('DOCUMENT_HEADER_L2', 'MINISTERE DE LA FORMATION PROFESSIONNELLE ET METIERS'),

    'line3' => env('DOCUMENT_HEADER_L3', 'COORDINATION NATIONALE DES ETABLISSEMENTS « EPC – EDS – APROAC »'),

    'line4' => env('DOCUMENT_HEADER_L4', 'BP 1756 KIN /GOMBE'),

    'line5' => env('DOCUMENT_HEADER_L5', 'N° d\'agrément : ARRETE MINISTERIEL N°566/CABMIN/MIN.FPM/AKK/KMJ/maf/2023 DU 20/12/2023'),

];
