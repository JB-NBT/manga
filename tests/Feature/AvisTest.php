<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Manga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\SetsUpRoles;
use Tests\TestCase;

class AvisTest extends TestCase
{
    use RefreshDatabase, SetsUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_utilisateur_peut_laisser_un_avis(): void
    {
        $user = $this->createUser();
        $owner = $this->createUser();
        $manga = Manga::factory()->create(['user_id' => $owner->id, 'est_public' => true]);

        $this->actingAs($user)
            ->post(route('avis.store', $manga), ['note' => 8, 'commentaire' => 'Super manga !'])
            ->assertRedirect();

        $this->assertDatabaseHas('avis', [
            'manga_id' => $manga->id,
            'user_id' => $user->id,
            'note' => 8,
        ]);
    }

    public function test_utilisateur_ne_peut_pas_laisser_deux_avis_sur_le_meme_manga(): void
    {
        $user = $this->createUser();
        $owner = $this->createUser();
        $manga = Manga::factory()->create(['user_id' => $owner->id, 'est_public' => true]);

        Avis::create(['manga_id' => $manga->id, 'user_id' => $user->id, 'note' => 7, 'modere' => false]);

        $this->actingAs($user)
            ->post(route('avis.store', $manga), ['note' => 9])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('avis', 1);
    }

    public function test_note_inferieure_a_1_est_invalide(): void
    {
        $user = $this->createUser();
        $manga = Manga::factory()->create(['user_id' => $this->createUser()->id, 'est_public' => true]);

        $this->actingAs($user)
            ->post(route('avis.store', $manga), ['note' => 0])
            ->assertSessionHasErrors('note');
    }

    public function test_note_superieure_a_10_est_invalide(): void
    {
        $user = $this->createUser();
        $manga = Manga::factory()->create(['user_id' => $this->createUser()->id, 'est_public' => true]);

        $this->actingAs($user)
            ->post(route('avis.store', $manga), ['note' => 11])
            ->assertSessionHasErrors('note');
    }

    public function test_impossible_de_noter_un_manga_prive(): void
    {
        $user = $this->createUser();
        $manga = Manga::factory()->create(['user_id' => $this->createUser()->id, 'est_public' => false]);

        $this->actingAs($user)
            ->post(route('avis.store', $manga), ['note' => 5])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('avis', 0);
    }

    public function test_non_connecte_ne_peut_pas_laisser_un_avis(): void
    {
        $manga = Manga::factory()->create(['user_id' => $this->createUser()->id, 'est_public' => true]);

        $this->post(route('avis.store', $manga), ['note' => 5])
            ->assertRedirect(route('login'));
    }

    public function test_note_moyenne_est_mise_a_jour_apres_un_avis(): void
    {
        $user = $this->createUser();
        $manga = Manga::factory()->create(['user_id' => $this->createUser()->id, 'est_public' => true]);

        $this->actingAs($user)
            ->post(route('avis.store', $manga), ['note' => 6]);

        $this->assertDatabaseHas('mangas', ['id' => $manga->id, 'note_moyenne' => 6.0, 'nombre_avis' => 1]);
    }
}
