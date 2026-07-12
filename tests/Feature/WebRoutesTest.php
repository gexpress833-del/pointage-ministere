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

    public function test_root_shows_landing_for_authenticated_agent(): void
    {
        $user = User::factory()->agent()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertOk();
    }

    public function test_root_shows_landing_for_authenticated_coordinateur(): void
    {
        $user = User::factory()->coordinateur()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertOk();
    }

    public function test_presence_routes_require_authentication(): void
    {
        $login = route('login');

        $this->get(route('presence.sign'))->assertRedirect($login);
        $this->get(route('presence.dashboard'))->assertRedirect($login);
        $this->get(route('presence.historique'))->assertRedirect($login);
    }
}
