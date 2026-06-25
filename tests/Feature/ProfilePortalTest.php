<?php

namespace Tests\Feature;

use App\Models\Bureau;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_view_profile(): void
    {
        $bureau = Bureau::factory()->create();
        $agent = User::factory()->agent()->create([
            'bureau_id' => $bureau->id,
            'nom' => 'Jean Test',
        ]);

        $this->actingAs($agent)
            ->get(route('presence.profile'))
            ->assertOk()
            ->assertSee('Jean Test', false)
            ->assertSee('Téléphone', false);
    }

    public function test_agent_can_update_phone_and_address_only(): void
    {
        $bureau = Bureau::factory()->create();
        $agent = User::factory()->agent()->create([
            'bureau_id' => $bureau->id,
            'nom' => 'Nom Original',
            'telephone' => null,
            'adresse_residence' => null,
        ]);

        $this->actingAs($agent)
            ->patch(route('presence.profile.update'), [
                'telephone' => '+243900000000',
                'adresse_residence' => "12 av. Test\nKinshasa",
                'nom' => 'Hacker',
                'email' => 'evil@example.com',
            ])
            ->assertRedirect(route('presence.profile'));

        $agent->refresh();
        $this->assertSame('+243900000000', $agent->telephone);
        $this->assertStringContainsString('12 av. Test', $agent->adresse_residence ?? '');
        $this->assertSame('Nom Original', $agent->nom);
        $this->assertNotSame('evil@example.com', $agent->email);
    }

    public function test_guest_cannot_access_profile(): void
    {
        $this->get(route('presence.profile'))
            ->assertRedirect();
    }
}
