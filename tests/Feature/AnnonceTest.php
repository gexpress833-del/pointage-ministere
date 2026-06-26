<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Bureau;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnonceTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_sees_published_annonce_on_dashboard(): void
    {
        $bureau = Bureau::factory()->create();
        $admin = User::factory()->administrateur()->create();
        $agent = User::factory()->agent()->create(['bureau_id' => $bureau->id]);

        Annonce::factory()->create([
            'titre' => 'Info maintenance',
            'contenu' => 'Le système sera indisponible dimanche.',
            'published_at' => now()->subMinute(),
            'created_by' => $admin->id,
        ]);

        $this->actingAs($agent)
            ->get(route('presence.dashboard'))
            ->assertOk()
            ->assertSee('Info maintenance', false)
            ->assertSee('indisponible', false);
    }

    public function test_draft_annonce_not_shown_on_portal(): void
    {
        $bureau = Bureau::factory()->create();
        $admin = User::factory()->administrateur()->create();
        $agent = User::factory()->agent()->create(['bureau_id' => $bureau->id]);

        Annonce::factory()->brouillon()->create([
            'titre' => 'Brouillon secret',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($agent)
            ->get(route('presence.dashboard'))
            ->assertOk()
            ->assertDontSee('Brouillon secret', false);
    }

    public function test_admin_can_open_annonces_list(): void
    {
        $admin = User::factory()->administrateur()->create();

        $this->actingAs($admin)
            ->get('/admin/annonces')
            ->assertOk();
    }

    public function test_coordinateur_cannot_open_annonces_crud(): void
    {
        $coord = User::factory()->coordinateur()->create();

        $this->actingAs($coord)
            ->get('/admin/annonces')
            ->assertForbidden();
    }
}
