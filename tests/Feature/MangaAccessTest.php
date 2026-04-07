<?php

namespace Tests\Feature;

use App\Models\Manga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\SetsUpRoles;
use Tests\TestCase;

class MangaAccessTest extends TestCase
{
    use RefreshDatabase, SetsUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_manga_public_visible_par_un_visiteur(): void
    {
        $manga = Manga::factory()->create(['user_id' => $this->createUser()->id, 'est_public' => true]);

        $this->get(route('mangas.show', $manga))
            ->assertStatus(200);
    }

    public function test_manga_prive_non_accessible_a_un_visiteur(): void
    {
        $manga = Manga::factory()->create(['user_id' => $this->createUser()->id, 'est_public' => false]);

        $this->get(route('mangas.show', $manga))
            ->assertStatus(403);
    }

    public function test_manga_prive_accessible_par_son_proprietaire(): void
    {
        $owner = $this->createUser();
        $manga = Manga::factory()->create(['user_id' => $owner->id, 'est_public' => false]);

        $this->actingAs($owner)
            ->get(route('mangas.show', $manga))
            ->assertStatus(200);
    }

    public function test_manga_prive_accessible_par_un_moderateur(): void
    {
        $manga = Manga::factory()->create(['user_id' => $this->createUser()->id, 'est_public' => false]);

        $this->actingAs($this->createModerator())
            ->get(route('mangas.show', $manga))
            ->assertStatus(200);
    }

    public function test_manga_prive_non_accessible_par_un_autre_utilisateur(): void
    {
        $manga = Manga::factory()->create(['user_id' => $this->createUser()->id, 'est_public' => false]);

        $this->actingAs($this->createUser())
            ->get(route('mangas.show', $manga))
            ->assertStatus(403);
    }

    public function test_bibliotheque_publique_accessible_sans_connexion(): void
    {
        $this->get(route('home'))
            ->assertStatus(200);
    }
}
