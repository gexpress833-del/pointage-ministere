<?php

namespace App\Http\Controllers;

use App\Models\SessionPresence;
use App\Models\User;
use App\Services\PresenceReportPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Téléchargement du rapport PDF journalier pour une session.
     * L'authentification est assurée par le middleware 'auth' sur les routes.
     */
    public function dailyPdf(SessionPresence $session)
    {
        $this->authorizeViewReport();

        return app(PresenceReportPdf::class)->dailyReport($session);
    }

    /**
     * Téléchargement du rapport PDF mensuel.
     */
    public function monthlyPdf(Request $request)
    {
        $this->authorizeViewReport();
        $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'bureau_id' => 'nullable|exists:bureaux,id',
        ]);
        $bureauId = Auth::user()?->isChefBureau() ? Auth::user()->bureau_id : $request->bureau_id;

        return app(PresenceReportPdf::class)->monthlyReport(
            (int) $request->year,
            (int) $request->month,
            $bureauId
        );
    }

    public function userPdf(Request $request, User $user)
    {
        $this->authorizeViewReport();
        $validated = $request->validate([
            'start' => ['nullable', 'date_format:Y-m-d'],
            'end' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start'],
        ]);

        return app(PresenceReportPdf::class)->userReport(
            $user,
            $validated['start'] ?? null,
            $validated['end'] ?? null
        );
    }

    public function datePdf(Request $request)
    {
        $this->authorizeViewReport();
        $request->validate([
            'date' => 'required|date|date_format:Y-m-d',
        ]);

        return app(PresenceReportPdf::class)->dateReport($request->date);
    }

    private function authorizeViewReport(): void
    {
        $user = Auth::user();
        if (! $user->isAdministrateur() && ! $user->isCoordinateur() && ! $user->isChefBureau()) {
            abort(403);
        }
    }
}
