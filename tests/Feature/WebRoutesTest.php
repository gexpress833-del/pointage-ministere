<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_is_ok_for_guests(): void
    {
        $this->get('/')->assertOk()->assertSee(config('app.name'), false);
    }

    public function test_root_redirects_agent_to_dashboard(): void
    {
        $user = User::factory()->agent()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('presence.dashboard'));
    }

    public function test_root_redirects_coordinateur_to_admin_panel(): void
    {
        $user = User::factory()->coordinateur()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect('/admin');
    }

    public function test_presence_routes_require_authentication(): void
    {
        $login = route('filament.admin.auth.login');

        $this->get(route('presence.sign'))->assertRedirect($login);
        $this->get(route('presence.dashboard'))->assertRedirect($login);
        $this->get(route('presence.historique'))->assertRedirect($login);
    }
}
