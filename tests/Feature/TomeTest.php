<?php

namespace Tests\Feature;

use App\Models\Manga;
use App\Models\Tome;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\SetsUpRoles;
use Tests\TestCase;

class TomeTest extends TestCase
{
    use RefreshDatabase, SetsUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    private function createTome(int $userId): array
    {
        $manga = Manga::factory()->create(['user_id' => $userId]);
        $tome = Tome::create(['manga_id' => $manga->id, 'numero' => 1, 'possede' => false]);
        return [$manga, $tome];
    }

    public function test_date_achat_dans_le_futur_est_invalide(): void
    {
        $user = $this->createUser();
        [, $tome] = $this->createTome($user->id);

        $this->actingAs($user)
            ->put(route('tomes.update', $tome), [
                'possede' => true,
                'date_achat' => now()->addDay()->format('Y-m-d'),
            ])
            ->assertSessionHasErrors('date_achat');
    }

    public function test_date_achat_aujourd_hui_est_valide(): void
    {
        $user = $this->createUser();
        [, $tome] = $this->createTome($user->id);

        $this->actingAs($user)
            ->put(route('tomes.update', $tome), [
                'possede' => true,
                'date_achat' => now()->format('Y-m-d'),
            ])
            ->assertRedirect();
    }

    public function test_date_achat_dans_le_passe_est_valide(): void
    {
        $user = $this->createUser();
        [, $tome] = $this->createTome($user->id);

        $this->actingAs($user)
            ->put(route('tomes.update', $tome), [
                'possede' => true,
                'date_achat' => now()->subYear()->format('Y-m-d'),
            ])
            ->assertRedirect();
    }

    public function test_autre_utilisateur_ne_peut_pas_modifier_un_tome(): void
    {
        $owner = $this->createUser();
        $autre = $this->createUser();
        [, $tome] = $this->createTome($owner->id);

        $this->actingAs($autre)
            ->put(route('tomes.update', $tome), ['possede' => true, 'date_achat' => null])
            ->assertStatus(403);
    }

    public function test_toggle_possession_met_a_jour_la_date_achat(): void
    {
        $user = $this->createUser();
        [, $tome] = $this->createTome($user->id);

        $this->actingAs($user)
            ->post(route('tomes.toggle', $tome))
            ->assertRedirect();

        $tome->refresh();
        $this->assertTrue($tome->possede);
        $this->assertNotNull($tome->date_achat);
    }
}
