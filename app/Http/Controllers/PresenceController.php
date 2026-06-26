<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use App\Models\Presence;
use App\Models\SessionPresence;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresenceController extends Controller
{
    /**
     * face-api.js compare des descripteurs avec une distance euclidienne (plus bas = plus proche).
     * Une même personne est souvent entre ~0,35 et ~0,55 selon lumière / caméra.
     * Les anciennes valeurs type 0,15 (confusion avec « 15 % ») rendaient la validation impossible.
     */
    private static function normalizeFaceDistanceThreshold(float $raw): float
    {
        if ($raw <= 0.0 || $raw > 1.5) {
            return 0.55;
        }
        if ($raw < 0.35) {
            return 0.55;
        }

        return min($raw, 0.85);
    }

    /**
     * Affiche la page de signature de présence.
     */
    public function showSign(Request $request)
    {
        $user = Auth::user();

        if (! in_array($user->role, [User::ROLE_AGENT, User::ROLE_CHEF_BUREAU], true)) {
            return view('presence.sign-blocked', [
                'raison' => 'role',
                'titre' => 'Accès non autorisé',
                'message' => 'La signature de présence est réservée aux agents et aux chefs de bureau. Utilisez le tableau de bord pour la gestion.',
            ]);
        }

        // Vérification photo de référence
        if (! $user->photo_reference || ! Storage::disk('local')->exists($user->photo_reference)) {
            return view('presence.sign-blocked', [
                'raison' => 'photo',
                'titre' => 'Photo de référence manquante',
                'message' => 'Votre photo de référence n\'a pas encore été configurée. Contactez l\'administrateur pour qu\'il ajoute votre photo dans votre profil.',
            ]);
        }

        $sessionJour = SessionPresence::where('date', Carbon::today())->first();

        if (! $sessionJour) {
            return view('presence.sign-blocked', [
                'raison' => 'session',
                'titre' => 'Aucune session pour aujourd\'hui',
                'message' => 'Il n\'existe pas encore de session de présence pour aujourd\'hui. Revenez plus tard ou contactez votre responsable.',
            ]);
        }

        $presenceJour = Presence::where('session_id', $sessionJour->id)
            ->where('user_id', $user->id)
            ->first();

        // Pointage d'arrivée : session doit être ouverte
        if (! $presenceJour) {
            if ($sessionJour->statut !== SessionPresence::STATUT_OUVERTE) {
                return view('presence.sign-blocked', [
                    'raison' => 'session',
                    'titre' => 'Session fermée',
                    'message' => 'La session de présence d\'aujourd\'hui est fermée. Vous ne pouvez plus enregistrer une arrivée.',
                ]);
            }

            return $this->renderSignView($user, $sessionJour, 'arrival');
        }

        // Pointage de départ : autorisé tant que l'arrivée est enregistrée et le départ manquant
        if ($presenceJour->heure_depart !== null) {
            return view('presence.sign-blocked', [
                'raison' => 'signe',
                'titre' => 'Journée complète',
                'message' => 'Vous avez déjà enregistré votre arrivée et votre départ pour aujourd\'hui. Consultez votre historique pour le détail.',
            ]);
        }

        return $this->renderSignView($user, $sessionJour, 'departure');
    }

    /**
     * @param  'arrival'|'departure'  $signMode
     */
    private function renderSignView(User $user, SessionPresence $session, string $signMode)
    {
        $referencePhotoUrl = route('presence.reference-photo').'?t='.time();
        $rawSeuil = (float) Parametre::getValue(Parametre::CLE_SEUIL_RECONNAISSANCE, '0.55');
        $seuilReconnaissance = self::normalizeFaceDistanceThreshold($rawSeuil);

        return view('presence.sign', [
            'agentPhotoUrl' => route('users.photo-reference', $user),
            'referencePhotoUrl' => $referencePhotoUrl,
            'sessionId' => $session->id,
            'seuilReconnaissance' => $seuilReconnaissance,
            'signMode' => $signMode,
            'submitSignUrl' => $signMode === 'departure'
                ? route('presence.sign-depart.submit')
                : route('presence.sign.submit'),
        ]);
    }

    /**
     * Retourne la photo de référence de l'utilisateur connecté (pour face-api).
     */
    public function referencePhoto(Request $request)
    {
        $user = Auth::user();
        if (! $user->photo_reference) {
            abort(404);
        }
        $path = Storage::disk('local')->path($user->photo_reference);
        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Tableau de bord agent / chef : synthèse du mois et accès rapides.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $moisCourant = now()->format('Y-m');
        $statsMois = $this->statsPourMois($user, $moisCourant);

        return view('presence.dashboard', [
            'user' => $user,
            'moisCourant' => $moisCourant,
            'statsMois' => $statsMois,
            'sessionOuverte' => $this->sessionOuvertePour($user),
            'besoinPointerDepart' => $this->besoinPointerDepartPour($user),
        ]);
    }

    /**
     * Historique détaillé avec filtres (mois, statut).
     */
    public function historique(Request $request)
    {
        $user = Auth::user();

        $moisSelectionne = $request->get('month', now()->format('Y-m'));
        $statutFiltre = $request->get('statut', '');
        if (! in_array($statutFiltre, ['', 'present', 'retard'], true)) {
            $statutFiltre = '';
        }

        $presencesQuery = Presence::with('session')
            ->where('user_id', $user->id)
            ->whereHas('session', function (Builder $q) use ($moisSelectionne) {
                $q->whereYear('date', substr($moisSelectionne, 0, 4))
                    ->whereMonth('date', substr($moisSelectionne, 5, 2));
            });

        if ($statutFiltre === 'present') {
            $presencesQuery->where('statut', Presence::STATUT_PRESENT);
        } elseif ($statutFiltre === 'retard') {
            $presencesQuery->where('statut', Presence::STATUT_RETARD);
        }

        $presences = $presencesQuery->orderByDesc(
            SessionPresence::select('date')
                ->whereColumn('id', 'presences.session_id')
                ->limit(1)
        )->get();

        $moisDisponibles = $this->moisDisponiblesPour($user);
        $statsMois = $this->statsPourMois($user, $moisSelectionne);

        return view('presence.historique', [
            'user' => $user,
            'presences' => $presences,
            'moisDisponibles' => $moisDisponibles,
            'moisSelectionne' => $moisSelectionne,
            'statutFiltre' => $statutFiltre,
            'statsMois' => $statsMois,
            'sessionOuverte' => $this->sessionOuvertePour($user),
            'besoinPointerDepart' => $this->besoinPointerDepartPour($user),
        ]);
    }

    /**
     * @return array{presents: int, retards: int, absences: int}
     */
    private function statsPourMois(User $user, string $moisYyyyMm): array
    {
        $sessionsIds = SessionPresence::whereYear('date', substr($moisYyyyMm, 0, 4))
            ->whereMonth('date', substr($moisYyyyMm, 5, 2))
            ->pluck('id');

        $totalSessions = $sessionsIds->count();
        $signeCount = Presence::where('user_id', $user->id)->whereIn('session_id', $sessionsIds)->count();
        $retardCount = Presence::where('user_id', $user->id)->whereIn('session_id', $sessionsIds)
            ->where('statut', Presence::STATUT_RETARD)->count();
        $presentCount = $signeCount - $retardCount;

        return [
            'presents' => $presentCount,
            'retards' => $retardCount,
            'absences' => max(0, $totalSessions - $signeCount),
        ];
    }

    /**
     * @return list<string>
     */
    private function moisDisponiblesPour(User $user): array
    {
        $moisDisponibles = Presence::with('session')
            ->where('user_id', $user->id)
            ->whereHas('session')
            ->get()
            ->map(fn ($p) => $p->session->date->format('Y-m'))
            ->unique()
            ->sort()
            ->reverse()
            ->values()
            ->toArray();

        $moisCourant = now()->format('Y-m');
        if (! in_array($moisCourant, $moisDisponibles)) {
            array_unshift($moisDisponibles, $moisCourant);
        }

        return $moisDisponibles;
    }

    private function sessionOuvertePour(User $user): bool
    {
        return SessionPresence::where('date', today())
            ->where('statut', SessionPresence::STATUT_OUVERTE)
            ->exists() && ! Presence::where('user_id', $user->id)
                ->whereHas('session', fn ($q) => $q->where('date', today()))
                ->exists();
    }

    private function besoinPointerDepartPour(User $user): bool
    {
        $session = SessionPresence::where('date', today())->first();
        if (! $session) {
            return false;
        }

        $presence = Presence::where('session_id', $session->id)
            ->where('user_id', $user->id)
            ->first();

        return $presence !== null && $presence->heure_depart === null;
    }

    /**
     * Enregistre la signature de présence (après validation faciale côté client).
     */
    public function sign(Request $request)
    {
        $request->validate(['session_id' => 'required|integer']);

        $user = Auth::user();

        if (! in_array($user->role, [User::ROLE_AGENT, User::ROLE_CHEF_BUREAU], true)) {
            return response()->json(['success' => false, 'message' => 'Action non autorisée pour ce rôle.'], 403);
        }
        $session = SessionPresence::findOrFail($request->session_id);

        if ($session->statut !== SessionPresence::STATUT_OUVERTE) {
            return response()->json(['success' => false, 'message' => 'Session fermée.'], 403);
        }

        if ($session->date->format('Y-m-d') !== Carbon::today()->format('Y-m-d')) {
            return response()->json(['success' => false, 'message' => 'Session invalide.'], 403);
        }

        $exists = Presence::where('session_id', $session->id)->where('user_id', $user->id)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Déjà signé.'], 403);
        }

        $photoCapture = null;
        if ($request->has('photo_capture') && $request->photo_capture) {
            $base64 = $request->photo_capture;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $m)) {
                $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                $data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64));
                if ($data !== false) {
                    $filename = 'presences/'.$session->id.'_'.$user->id.'_'.time().'.'.$ext;
                    Storage::disk('local')->put($filename, $data);
                    $photoCapture = $filename;
                }
            }
        }

        $heureArrivee = Carbon::now();
        $heureLimiteStr = Parametre::heureLimiteRetard();
        $heureLimiteRetard = substr_count($heureLimiteStr, ':') >= 2
            ? $session->date->copy()->setTimeFromTimeString($heureLimiteStr)
            : $session->date->copy()->setTimeFromTimeString($heureLimiteStr.':00');
        $statut = $heureArrivee->gt($heureLimiteRetard) ? Presence::STATUT_RETARD : Presence::STATUT_PRESENT;

        Presence::create([
            'session_id' => $session->id,
            'user_id' => $user->id,
            'heure_arrivee' => $heureArrivee->format('H:i:s'),
            'photo_capture' => $photoCapture,
            'statut' => $statut,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Présence enregistrée.',
            'heure' => $heureArrivee->format('H:i'),
            'kind' => 'arrival',
        ]);
    }

    /**
     * Enregistre l'heure de départ (même jour que l'arrivée), après reconnaissance faciale.
     */
    public function signDepart(Request $request)
    {
        $request->validate(['session_id' => 'required|integer']);

        $user = Auth::user();

        if (! in_array($user->role, [User::ROLE_AGENT, User::ROLE_CHEF_BUREAU], true)) {
            return response()->json(['success' => false, 'message' => 'Action non autorisée pour ce rôle.'], 403);
        }

        $session = SessionPresence::findOrFail($request->session_id);

        if ($session->date->format('Y-m-d') !== Carbon::today()->format('Y-m-d')) {
            return response()->json(['success' => false, 'message' => 'Session invalide pour aujourd\'hui.'], 403);
        }

        $presence = Presence::where('session_id', $session->id)->where('user_id', $user->id)->first();
        if (! $presence) {
            return response()->json(['success' => false, 'message' => 'Aucune arrivée enregistrée pour aujourd\'hui.'], 403);
        }

        if ($presence->heure_depart !== null) {
            return response()->json(['success' => false, 'message' => 'Départ déjà enregistré.'], 403);
        }

        $heureDepart = Carbon::now();
        $hDepart = $heureDepart->format('H:i:s');
        $hArrivee = Carbon::parse($presence->heure_arrivee)->format('H:i:s');
        // Comparaison sur l'horloge du même jour (cas bureau). Les shifts nuit > minuit restent une évolution ultérieure.
        if ($hDepart < $hArrivee) {
            return response()->json(['success' => false, 'message' => 'L\'heure de départ ne peut pas précéder l\'heure d\'arrivée.'], 422);
        }

        if ($request->has('photo_capture') && $request->photo_capture) {
            $base64 = $request->photo_capture;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $m)) {
                $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                $data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64));
                if ($data !== false) {
                    $filename = 'presences/depart_'.$session->id.'_'.$user->id.'_'.time().'.'.$ext;
                    Storage::disk('local')->put($filename, $data);
                }
            }
        }

        $presence->update([
            'heure_depart' => $heureDepart->format('H:i:s'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Départ enregistré.',
            'heure' => $heureDepart->format('H:i'),
            'kind' => 'departure',
        ]);
    }
}
