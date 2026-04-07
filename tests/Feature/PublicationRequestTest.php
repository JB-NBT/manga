<?php

namespace Tests\Feature;

use App\Models\Manga;
use App\Models\PublicationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\SetsUpRoles;
use Tests\TestCase;

class PublicationRequestTest extends TestCase
{
    use RefreshDatabase, SetsUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_moderateur_ne_peut_pas_approuver_sa_propre_demande(): void
    {
        $moderateur = $this->createModerator();
        $manga = Manga::factory()->create(['user_id' => $moderateur->id, 'est_public' => false]);
        $demande = PublicationRequest::create([
            'manga_id' => $manga->id,
            'user_id' => $moderateur->id,
            'statut' => 'en_attente',
        ]);

        $this->actingAs($moderateur)
            ->post(route('admin.publication.approve', $demande))
            ->assertStatus(403);
    }

    public function test_moderateur_ne_peut_pas_refuser_sa_propre_demande(): void
    {
        $moderateur = $this->createModerator();
        $manga = Manga::factory()->create(['user_id' => $moderateur->id, 'est_public' => false]);
        $demande = PublicationRequest::create([
            'manga_id' => $manga->id,
            'user_id' => $moderateur->id,
            'statut' => 'en_attente',
        ]);

        $this->actingAs($moderateur)
            ->post(route('admin.publication.reject', $demande), ['message_admin' => 'Refus.'])
            ->assertStatus(403);
    }

    public function test_moderateur_peut_approuver_la_demande_dun_autre_utilisateur(): void
    {
        $user = $this->createUser();
        $moderateur = $this->createModerator();
        $manga = Manga::factory()->create(['user_id' => $user->id, 'est_public' => false]);
        $demande = PublicationRequest::create([
            'manga_id' => $manga->id,
            'user_id' => $user->id,
            'statut' => 'en_attente',
        ]);

        $this->actingAs($moderateur)
            ->post(route('admin.publication.approve', $demande))
            ->assertRedirect();

        $this->assertDatabaseHas('mangas', ['id' => $manga->id, 'est_public' => true]);
        $this->assertDatabaseHas('publication_requests', ['id' => $demande->id, 'statut' => 'approuve']);
    }

    public function test_admin_peut_approuver_sa_propre_demande(): void
    {
        $admin = $this->createAdmin();
        $manga = Manga::factory()->create(['user_id' => $admin->id, 'est_public' => false]);
        $demande = PublicationRequest::create([
            'manga_id' => $manga->id,
            'user_id' => $admin->id,
            'statut' => 'en_attente',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.publication.approve', $demande))
            ->assertRedirect();

        $this->assertDatabaseHas('mangas', ['id' => $manga->id, 'est_public' => true]);
    }

    public function test_utilisateur_simple_ne_peut_pas_approuver(): void
    {
        $user = $this->createUser();
        $user2 = $this->createUser();
        $manga = Manga::factory()->create(['user_id' => $user2->id, 'est_public' => false]);
        $demande = PublicationRequest::create([
            'manga_id' => $manga->id,
            'user_id' => $user2->id,
            'statut' => 'en_attente',
        ]);

        $this->actingAs($user)
            ->post(route('admin.publication.approve', $demande))
            ->assertStatus(403);
    }

    public function test_non_connecte_ne_peut_pas_approuver(): void
    {
        $user = $this->createUser();
        $manga = Manga::factory()->create(['user_id' => $user->id, 'est_public' => false]);
        $demande = PublicationRequest::create([
            'manga_id' => $manga->id,
            'user_id' => $user->id,
            'statut' => 'en_attente',
        ]);

        $this->post(route('admin.publication.approve', $demande))
            ->assertRedirect(route('login'));
    }
}
