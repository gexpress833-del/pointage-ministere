<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use App\Models\Presence;
use App\Models\SessionPresence;
use App\Models\User;
use App\Services\ImagekitService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        if ($user->isExemptePointage()) {
            return view('presence.sign-blocked', [
                'raison' => 'exempte',
                'titre' => 'Pointage non requis',
                'message' => "En tant qu'administrateur, vous êtes exempté du pointage de présence.",
            ]);
        }

        if (! in_array($user->role, [User::ROLE_AGENT, User::ROLE_CHEF_BUREAU, User::ROLE_ADMIN, User::ROLE_SECRETAIRE, User::ROLE_COORDINATEUR], true)) {
            return view('presence.sign-blocked', [
                'raison' => 'role',
                'titre' => 'Accès non autorisé',
                'message' => 'La signature de présence est réservée aux utilisateurs enregistrés.',
            ]);
        }

        // Vérification photo de référence
        if (! $user->photo_reference) {
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

        // Si c'est une URL Imagekit, proxifier l'image pour éviter les problèmes CORS
        // (face-api.js doit lire les pixels via canvas, ce qui nécessite same-origin ou CORS)
        if (ImagekitService::isImagekitUrl($user->photo_reference)) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(15)->get($user->photo_reference);
                if ($response->successful()) {
                    return response($response->body(), 200, [
                        'Content-Type' => $response->header('Content-Type', 'image/jpeg'),
                        'Cache-Control' => 'public, max-age=3600',
                        'Access-Control-Allow-Origin' => '*',
                    ]);
                }
            } catch (\Exception $e) {
                abort(404);
            }
        }

        // Fallback: fichier local (anciennes photos pas encore migrées)
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

        $sessionJour = SessionPresence::where('date', Carbon::today())->first();

        return view('presence.dashboard', [
            'user' => $user,
            'moisCourant' => $moisCourant,
            'statsMois' => $statsMois,
            'sessionJour' => $sessionJour,
            'sessionOuverte' => $sessionJour?->isOuverte() ?? false,
            'besoinPointerDepart' => $this->besoinPointerDepartPour($user),
            'exemptePointage' => $user->isExemptePointage(),
            'serverTimestamp' => now()->timestamp * 1000,
        ]);
    }

    /**
     * Historique détaillé avec filtres (mois, statut).
     */
    public function historique(Request $request)
    {
        $user = Auth::user();

        $moisSelectionne = $request->get('month', now()->format('Y-m'));
        if (! is_string($moisSelectionne) || ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $moisSelectionne)) {
            $moisSelectionne = now()->format('Y-m');
        }
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
        $sessions = SessionPresence::whereYear('date', substr($moisYyyyMm, 0, 4))
            ->whereMonth('date', substr($moisYyyyMm, 5, 2))
            ->get();

        $sessionsIds = $sessions->pluck('id');

        $signeCount = Presence::where('user_id', $user->id)->whereIn('session_id', $sessionsIds)->count();
        $retardCount = Presence::where('user_id', $user->id)->whereIn('session_id', $sessionsIds)
            ->where('statut', Presence::STATUT_RETARD)->count();
        $presentCount = $signeCount - $retardCount;

        $aujourdhui = Carbon::today();
        $sessionsAvecPointages = Presence::whereIn('session_id', $sessionsIds)->pluck('session_id')->unique();
        $sessionsComptables = $sessions->filter(function ($s) use ($aujourdhui, $sessionsAvecPointages) {
            return $s->date->lessThan($aujourdhui)
                && $s->isFermee()
                && $sessionsAvecPointages->contains($s->id);
        })->count();

        return [
            'presents' => $presentCount,
            'retards' => $retardCount,
            'absences' => max(0, $sessionsComptables - $signeCount),
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
        $request->validate([
            'session_id' => ['required', 'integer'],
            'photo_capture' => ['nullable', 'string', 'max:8388608'],
        ]);

        $user = Auth::user();

        if ($user->isExemptePointage()) {
            return response()->json(['success' => false, 'message' => 'Vous êtes exempté du pointage.'], 403);
        }

        if (! in_array($user->role, [User::ROLE_AGENT, User::ROLE_CHEF_BUREAU, User::ROLE_ADMIN, User::ROLE_SECRETAIRE, User::ROLE_COORDINATEUR], true)) {
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
                $data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64), true);
                if ($data !== false) {
                    $fileName = 'presence_'.$session->id.'_'.$user->id.'_'.time().'.'.$ext;
                    try {
                        $imagekit = app(ImagekitService::class);
                        $result = $imagekit->uploadBase64($data, $fileName, '/presences');
                        $photoCapture = $result['url'];
                    } catch (\Throwable $e) {
                        $localPath = 'presences/'.$session->id.'_'.$user->id.'_'.time().'.'.$ext;
                        try {
                            Storage::disk('local')->put($localPath, $data);
                            $photoCapture = $localPath;
                        } catch (\Throwable $storageException) {
                            Log::warning('Capture de présence non stockée', [
                                'user_id' => $user->id,
                                'session_id' => $session->id,
                                'imagekit_error' => $e->getMessage(),
                                'storage_error' => $storageException->getMessage(),
                            ]);
                        }
                    }
                }
            }
        }

        $heureArrivee = Carbon::now();
        $todayDate = $session->date->copy();

        // Fenêtre d'arrivée : 07:59 – 11:59
        $heureOuvertureStr = Parametre::heureOuvertureSession();
        $heureFinArriveeStr = Parametre::heureFinArrivee();
        $heureLimiteRetardStr = Parametre::heureLimiteRetard();

        $heureOuverture = $todayDate->copy()->setTimeFromTimeString(
            substr_count($heureOuvertureStr, ':') >= 2 ? $heureOuvertureStr : $heureOuvertureStr.':00'
        );
        $heureFinArrivee = $todayDate->copy()->setTimeFromTimeString(
            substr_count($heureFinArriveeStr, ':') >= 2 ? $heureFinArriveeStr : $heureFinArriveeStr.':00'
        );
        $heureLimiteRetard = $todayDate->copy()->setTimeFromTimeString(
            substr_count($heureLimiteRetardStr, ':') >= 2 ? $heureLimiteRetardStr : $heureLimiteRetardStr.':00'
        );

        if ($heureArrivee->lt($heureOuverture)) {
            return response()->json(['success' => false, 'message' => 'Le pointage n\'est pas encore ouvert. Il ouvre à '.$heureOuvertureStr.'.'], 403);
        }
        if ($heureArrivee->gt($heureFinArrivee)) {
            return response()->json(['success' => false, 'message' => 'La fenêtre de pointage d\'arrivée est close (jusqu\'à '.$heureFinArriveeStr.').'], 403);
        }

        $statut = $heureArrivee->gt($heureLimiteRetard) ? Presence::STATUT_RETARD : Presence::STATUT_PRESENT;

        try {
            Presence::create([
                'session_id' => $session->id,
                'user_id' => $user->id,
                'heure_arrivee' => $heureArrivee->format('H:i:s'),
                'photo_capture' => $photoCapture,
                'statut' => $statut,
            ]);
        } catch (QueryException $e) {
            if (Presence::where('session_id', $session->id)->where('user_id', $user->id)->exists()) {
                return response()->json(['success' => false, 'message' => 'Présence déjà enregistrée.'], 409);
            }

            throw $e;
        }

        return response()->json([
            'success' => true,
            'message' => $statut === Presence::STATUT_RETARD ? 'Présence enregistrée (retard).' : 'Présence enregistrée.',
            'heure' => $heureArrivee->format('H:i'),
            'statut' => $statut,
            'kind' => 'arrival',
        ]);
    }

    /**
     * Enregistre l'heure de départ (même jour que l'arrivée), après reconnaissance faciale.
     */
    public function signDepart(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'integer'],
            'photo_capture' => ['nullable', 'string', 'max:8388608'],
        ]);

        $user = Auth::user();

        if ($user->isExemptePointage()) {
            return response()->json(['success' => false, 'message' => 'Vous êtes exempté du pointage.'], 403);
        }

        if (! in_array($user->role, [User::ROLE_AGENT, User::ROLE_CHEF_BUREAU, User::ROLE_ADMIN, User::ROLE_SECRETAIRE, User::ROLE_COORDINATEUR], true)) {
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
        $todayDate = $session->date->copy();

        // Fenêtre de départ : 15:59 – 23:59
        $heureDebutDepartStr = Parametre::heureDebutDepart();
        $heureFinDepartNormalStr = Parametre::heureFinDepartNormal();
        $heureFermetureStr = Parametre::heureFermetureSession();

        $heureDebutDepart = $todayDate->copy()->setTimeFromTimeString(
            substr_count($heureDebutDepartStr, ':') >= 2 ? $heureDebutDepartStr : $heureDebutDepartStr.':00'
        );
        $heureFinDepartNormal = $todayDate->copy()->setTimeFromTimeString(
            substr_count($heureFinDepartNormalStr, ':') >= 2 ? $heureFinDepartNormalStr : $heureFinDepartNormalStr.':00'
        );
        $heureFermeture = $todayDate->copy()->setTimeFromTimeString(
            substr_count($heureFermetureStr, ':') >= 2 ? $heureFermetureStr : $heureFermetureStr.':00'
        );

        if ($heureDepart->lt($heureDebutDepart)) {
            return response()->json(['success' => false, 'message' => 'Le pointage de départ n\'est pas encore ouvert. Il ouvre à '.$heureDebutDepartStr.'.'], 403);
        }
        if ($heureDepart->gt($heureFermeture)) {
            return response()->json(['success' => false, 'message' => 'La session est fermée. Le pointage de départ n\'est plus possible.'], 403);
        }

        $hArrivee = Carbon::parse($presence->heure_arrivee);
        $hArriveeFull = $todayDate->copy()->setTimeFromTimeString($hArrivee->format('H:i:s'));
        if ($heureDepart->lt($hArriveeFull)) {
            return response()->json(['success' => false, 'message' => 'L\'heure de départ ne peut pas précéder l\'heure d\'arrivée.'], 422);
        }

        // Déterminer le type de départ et calculer les heures supplémentaires
        $typeDepart = Presence::DEPART_NORMAL;
        $heureSupplementaire = null;

        if ($heureDepart->gt($heureFinDepartNormal)) {
            $typeDepart = Presence::DEPART_SUPPLEMENTAIRE;
            $suppMinutes = $heureFinDepartNormal->diffInMinutes($heureDepart);
            $h = intdiv($suppMinutes, 60);
            $m = $suppMinutes % 60;
            $heureSupplementaire = sprintf('%02d:%02d:00', $h, $m);
        }

        if ($request->has('photo_capture') && $request->photo_capture) {
            $base64 = $request->photo_capture;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $m)) {
                $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                $data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64), true);
                if ($data !== false) {
                    $fileName = 'depart_'.$session->id.'_'.$user->id.'_'.time().'.'.$ext;
                    try {
                        $imagekit = app(ImagekitService::class);
                        $imagekit->uploadBase64($data, $fileName, '/presences/departs');
                    } catch (\Throwable $e) {
                        $localPath = 'presences/depart_'.$session->id.'_'.$user->id.'_'.time().'.'.$ext;
                        try {
                            Storage::disk('local')->put($localPath, $data);
                        } catch (\Throwable $storageException) {
                            Log::warning('Capture de départ non stockée', [
                                'user_id' => $user->id,
                                'session_id' => $session->id,
                                'imagekit_error' => $e->getMessage(),
                                'storage_error' => $storageException->getMessage(),
                            ]);
                        }
                    }
                }
            }
        }

        $presence->update([
            'heure_depart' => $heureDepart->format('H:i:s'),
            'type_depart' => $typeDepart,
            'heure_supplementaire' => $heureSupplementaire,
        ]);

        $message = 'Départ enregistré.';
        if ($typeDepart === Presence::DEPART_SUPPLEMENTAIRE) {
            $suppStr = substr($heureSupplementaire, 0, 5);
            $message = 'Départ enregistré avec heures supplémentaires ('.$suppStr.').';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'heure' => $heureDepart->format('H:i'),
            'type_depart' => $typeDepart,
            'heure_supplementaire' => $heureSupplementaire,
            'kind' => 'departure',
        ]);
    }
}
