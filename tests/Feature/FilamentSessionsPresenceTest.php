<?php

namespace Tests\Feature;

use App\Filament\Resources\SessionsPresence\Pages\ListSessionsPresence;
use App\Models\SessionPresence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentSessionsPresenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sessions_presence_index_loads_for_admin(): void
    {
        $admin = User::factory()->administrateur()->create();

        $this->actingAs($admin)
            ->get('/admin/sessions-presence')
            ->assertOk();
    }

    public function test_sessions_presence_index_loads_for_coordinateur(): void
    {
        $user = User::factory()->coordinateur()->create();

        $this->actingAs($user)
            ->get('/admin/sessions-presence')
            ->assertOk();
    }

    public function test_sessions_presence_index_with_many_rows_still_ok(): void
    {
        $admin = User::factory()->administrateur()->create();

        for ($i = 0; $i < 15; $i++) {
            SessionPresence::create([
                'date' => now()->subDays($i)->toDateString(),
                'statut' => SessionPresence::STATUT_FERMEE,
                'opened_by' => $admin->id,
                'closed_by' => $admin->id,
                'closed_at' => now(),
            ]);
        }

        $this->actingAs($admin)
            ->get('/admin/sessions-presence')
            ->assertOk();
    }

    public function test_list_sessions_presence_livewire_component_mounts(): void
    {
        $admin = User::factory()->administrateur()->create();

        Livewire::actingAs($admin)
            ->test(ListSessionsPresence::class)
            ->assertSuccessful();
    }
}
