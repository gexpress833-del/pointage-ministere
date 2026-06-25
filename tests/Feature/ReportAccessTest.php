<?php

namespace Tests\Feature;

use App\Models\SessionPresence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_report_forbidden_for_agent(): void
    {
        $agent = User::factory()->agent()->create();

        $this->actingAs($agent)
            ->get(route('reports.monthly', ['year' => 2026, 'month' => 4]))
            ->assertForbidden();
    }

    public function test_daily_report_forbidden_for_agent(): void
    {
        $agent = User::factory()->agent()->create();
        $session = SessionPresence::create([
            'date' => today(),
            'statut' => SessionPresence::STATUT_OUVERTE,
            'opened_by' => $agent->id,
        ]);

        $this->actingAs($agent)
            ->get(route('reports.daily', $session))
            ->assertForbidden();
    }
}
