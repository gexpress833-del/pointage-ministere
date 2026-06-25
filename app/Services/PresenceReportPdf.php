<?php

namespace App\Services;

use App\Models\Parametre;
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
}
