<?php

namespace Tests\Feature;

use App\Models\Bureau;
use App\Models\Presence;
use App\Models\SessionPresence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PresenceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_access_sign_page(): void
    {
        $admin = User::factory()->administrateur()->create();

        $this->actingAs($admin)
            ->get(route('presence.sign'))
            ->assertOk()
            ->assertSee('Accès non autorisé', false);
    }

    public function test_agent_without_photo_gets_blocked_sign_page(): void
    {
        $bureau = Bureau::factory()->create();
        $agent = User::factory()->agent()->create(['bureau_id' => $bureau->id]);

        SessionPresence::create([
            'date' => today(),
            'statut' => SessionPresence::STATUT_OUVERTE,
            'opened_by' => $agent->id,
        ]);

        $this->actingAs($agent)
            ->get(route('presence.sign'))
            ->assertOk()
            ->assertSee('Photo de référence manquante', false);
    }

    public function test_agent_can_sign_when_session_open_and_photo_exists(): void
    {
        Storage::fake('local');

        $bureau = Bureau::factory()->create();
        $path = 'photos_reference/test_agent.jpg';
        Storage::disk('local')->put($path, 'fake-jpeg-bytes');

        $agent = User::factory()->agent()->create([
            'bureau_id' => $bureau->id,
            'photo_reference' => $path,
        ]);

        $session = SessionPresence::create([
            'date' => today(),
            'statut' => SessionPresence::STATUT_OUVERTE,
            'opened_by' => $agent->id,
        ]);

        $this->actingAs($agent)
            ->get(route('presence.sign'))
            ->assertOk()
            ->assertSee('Signer ma présence', false);

        $response = $this->actingAs($agent)->postJson(route('presence.sign.submit'), [
            'session_id' => $session->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('presences', [
            'session_id' => $session->id,
            'user_id' => $agent->id,
        ]);
    }

    public function test_agent_can_sign_depart_after_arrival(): void
    {
        Storage::fake('local');

        $bureau = Bureau::factory()->create();
        $path = 'photos_reference/test_depart.jpg';
        Storage::disk('local')->put($path, 'fake-jpeg-bytes');

        $agent = User::factory()->agent()->create([
            'bureau_id' => $bureau->id,
            'photo_reference' => $path,
        ]);

        $session = SessionPresence::create([
            'date' => today(),
            'statut' => SessionPresence::STATUT_OUVERTE,
            'opened_by' => $agent->id,
        ]);

        Presence::create([
            'session_id' => $session->id,
            'user_id' => $agent->id,
            'heure_arrivee' => now()->subMinutes(45)->format('H:i:s'),
            'statut' => Presence::STATUT_PRESENT,
        ]);

        $this->actingAs($agent)
            ->get(route('presence.sign'))
            ->assertOk()
            ->assertSee('Pointer mon départ', false);

        $response = $this->actingAs($agent)->postJson(route('presence.sign-depart.submit'), [
            'session_id' => $session->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('kind', 'departure');

        $this->assertDatabaseHas('presences', [
            'session_id' => $session->id,
            'user_id' => $agent->id,
        ]);

        $this->assertNotNull(Presence::where('session_id', $session->id)->where('user_id', $agent->id)->value('heure_depart'));
    }

    public function test_admin_cannot_post_sign(): void
    {
        $admin = User::factory()->administrateur()->create();
        $session = SessionPresence::create([
            'date' => today(),
            'statut' => SessionPresence::STATUT_OUVERTE,
            'opened_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->postJson(route('presence.sign.submit'), ['session_id' => $session->id])
            ->assertForbidden();

        $this->actingAs($admin)
            ->postJson(route('presence.sign-depart.submit'), ['session_id' => $session->id])
            ->assertForbidden();
    }

    public function test_dashboard_loads_for_agent(): void
    {
        $bureau = Bureau::factory()->create();
        $agent = User::factory()->agent()->create(['bureau_id' => $bureau->id]);

        $this->actingAs($agent)
            ->get(route('presence.dashboard'))
            ->assertOk()
            ->assertSee('Tableau de bord', false);
    }

    public function test_historique_lists_month_for_agent(): void
    {
        $bureau = Bureau::factory()->create();
        $agent = User::factory()->agent()->create(['bureau_id' => $bureau->id]);

        $session = SessionPresence::create([
            'date' => today(),
            'statut' => SessionPresence::STATUT_FERMEE,
            'opened_by' => $agent->id,
        ]);

        Presence::create([
            'session_id' => $session->id,
            'user_id' => $agent->id,
            'heure_arrivee' => '08:15:00',
            'statut' => Presence::STATUT_PRESENT,
        ]);

        $this->actingAs($agent)
            ->get(route('presence.historique', [
                'month' => now()->format('Y-m'),
                'statut' => '',
            ]))
            ->assertOk()
            ->assertSee('Filtres', false);
    }
}
