<?php

namespace App\Console\Commands;

use App\Models\Parametre;
use App\Models\SessionPresence;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AutoSessionCommand extends Command
{
    protected $signature = 'session:auto';

    protected $description = 'Ouvre ou ferme automatiquement la session de présence selon les horaires configurés.';

    public function handle(): int
    {
        $now = Carbon::now();
        $today = Carbon::today();

        $heureOuverture = Parametre::heureOuvertureSession();
        $heureFermeture = Parametre::heureFermetureSession();

        $ouverture = $today->copy()->setTimeFromTimeString($heureOuverture);
        $fermeture = $today->copy()->setTimeFromTimeString($heureFermeture);

        $session = SessionPresence::where('date', $today)->first();

        // Avant l'heure d'ouverture : rien à faire
        if ($now->lt($ouverture)) {
            $this->info("Avant l'heure d'ouverture ({$heureOuverture}). Rien à faire.");

            return self::SUCCESS;
        }

        // Après l'heure de fermeture : fermer la session si elle existe et est ouverte
        if ($now->gte($fermeture)) {
            if ($session && $session->isOuverte()) {
                $session->update([
                    'statut' => SessionPresence::STATUT_FERMEE,
                    'closed_at' => $now,
                ]);
                $this->info("Session fermée automatiquement à {$now->format('H:i')}.");
            } else {
                $this->info('Session déjà fermée ou inexistante.');
            }

            return self::SUCCESS;
        }

        // Entre ouverture et fermeture : créer/ouvrir la session si nécessaire
        if (! $session) {
            $session = SessionPresence::firstOrCreate(
                ['date' => $today->toDateString()],
                [
                    'statut' => SessionPresence::STATUT_OUVERTE,
                    'opened_by' => null,
                ]
            );
            $this->info($session->wasRecentlyCreated
                ? "Session ouverte automatiquement à {$now->format('H:i')}."
                : 'Session déjà créée par un autre processus.');
        } elseif (! $session->isOuverte()) {
            $session->update([
                'statut' => SessionPresence::STATUT_OUVERTE,
                'closed_by' => null,
                'closed_at' => null,
            ]);
            $this->info("Session réouverte automatiquement à {$now->format('H:i')}.");
        } else {
            $this->info('Session déjà ouverte.');
        }

        return self::SUCCESS;
    }
}
