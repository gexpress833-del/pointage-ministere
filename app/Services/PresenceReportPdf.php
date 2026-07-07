<?php

namespace App\Services;

use App\Models\Parametre;
use App\Models\Presence;
use App\Models\SessionPresence;
use App\Models\User;
use App\Support\PresenceCalendar;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class PresenceReportPdf
{
    /**
     * Génère le rapport PDF journalier pour une session.
     */
    public function dailyReport(SessionPresence $session)
    {
        $presences = $session->presences()->with('user.bureau')->orderBy('heure_arrivee')->get();
        $html = View::make('pdf.presence-journalier', [
            'session' => $session,
            'presences' => $presences,
            'date' => $session->date->format('d/m/Y'),
            'rapportTitle' => config('presence.report_title'),
            'heureReferenceDepart' => Parametre::heureReferenceDepart(),
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->stream('presence-'.$session->date->format('Y-m-d').'.pdf');
    }

    /**
     * Génère le rapport PDF mensuel (tous les agents, résumé par jour).
     */
    public function monthlyReport(int $year, int $month, ?int $bureauId = null)
    {
        $start = Carbon::createFromDate($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $sessions = SessionPresence::whereBetween('date', [$start, $end])
            ->when($bureauId, fn ($q) => $q->whereHas('presences.user', fn ($u) => $u->where('bureau_id', $bureauId)))
            ->orderBy('date')
            ->with(['presences.user.bureau'])
            ->get();

        $users = User::when($bureauId, fn ($q) => $q->where('bureau_id', $bureauId))
            ->whereIn('role', [User::ROLE_AGENT, User::ROLE_CHEF_BUREAU])
            ->orderBy('nom')
            ->get();

        $joursAvecSession = [];
        foreach ($sessions as $session) {
            $joursAvecSession[(int) $session->date->format('j')] = true;
        }

        $daysInMonth = $start->daysInMonth;
        $calendarByDay = [];
        for ($d = 1; $d <= 31; $d++) {
            if ($d > $daysInMonth) {
                $calendarByDay[$d] = null;
                continue;
            }
            $dt = Carbon::createFromDate($year, $month, $d)->startOfDay();
            $calendarByDay[$d] = [
                'future' => PresenceCalendar::estJourFutur($year, $month, $d),
                'ferie' => PresenceCalendar::libelleJourFerie($dt),
                'weekend' => $dt->isWeekend(),
                'weekday_abbr' => PresenceCalendar::abregeJourSemaine($dt),
            ];
        }

        $html = View::make('pdf.presence-mensuel', [
            'sessions' => $sessions,
            'users' => $users,
            'month' => $start->translatedFormat('F Y'),
            'year' => $year,
            'monthNum' => $month,
            'rapportTitle' => config('presence.report_title'),
            'joursAvecSession' => $joursAvecSession,
            'calendarByDay' => $calendarByDay,
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->stream('presence-mensuel-'.$year.'-'.str_pad((string) $month, 2, '0', STR_PAD_LEFT).'.pdf');
    }

    /**
     * Génère un rapport PDF pour un agent spécifique sur une période.
     */
    public function userReport(User $user, ?string $startDate = null, ?string $endDate = null)
    {
        $start = $startDate ? Carbon::parse($startDate) : now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate) : now()->endOfMonth();

        $presences = Presence::where('user_id', $user->id)
            ->whereHas('session', function ($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end]);
            })
            ->with('session')
            ->orderByDesc(function ($q) {
                $q->select('date')
                    ->from('sessions_presences')
                    ->whereColumn('id', 'presences.session_id')
                    ->limit(1);
            })
            ->get();

        $totalSessions = SessionPresence::whereBetween('date', [$start, $end])->count();
        $presentCount = $presences->where('statut', Presence::STATUT_PRESENT)->count();
        $retardCount = $presences->where('statut', Presence::STATUT_RETARD)->count();
        $absenceCount = max(0, $totalSessions - $presences->count());

        $html = View::make('pdf.presence-agent', [
            'user' => $user,
            'presences' => $presences,
            'startDate' => $start->format('d/m/Y'),
            'endDate' => $end->format('d/m/Y'),
            'presentCount' => $presentCount,
            'retardCount' => $retardCount,
            'absenceCount' => $absenceCount,
            'totalSessions' => $totalSessions,
            'rapportTitle' => config('presence.report_title'),
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->stream('presence-agent-'.$user->matricule.'-'.$start->format('Y-m-d').'-'.$end->format('Y-m-d').'.pdf');
    }

    /**
     * Génère un rapport PDF pour une date spécifique (tous les agents).
     */
    public function dateReport(string $date)
    {
        $dateCarbon = Carbon::parse($date);
        $session = SessionPresence::where('date', $dateCarbon)->first();

        $presences = collect();
        if ($session) {
            $presences = $session->presences()->with('user.bureau')->orderBy('heure_arrivee')->get();
        }

        $html = View::make('pdf.presence-journalier', [
            'session' => $session,
            'presences' => $presences,
            'date' => $dateCarbon->format('d/m/Y'),
            'rapportTitle' => config('presence.report_title'),
            'heureReferenceDepart' => Parametre::heureReferenceDepart(),
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->stream('presence-'.$dateCarbon->format('Y-m-d').'.pdf');
    }
}
