<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\SetsUpRoles;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase, SetsUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    private function createTicket(User $user): Ticket
    {
        return Ticket::create([
            'user_id' => $user->id,
            'sujet' => 'Problème de test',
            'description' => 'Description du problème.',
            'categorie' => 'bug',
            'priorite' => 'normale',
            'statut' => 'ouvert',
        ]);
    }

    public function test_moderateur_ne_peut_pas_repondre_a_son_propre_ticket(): void
    {
        $moderateur = $this->createModerator();
        $ticket = $this->createTicket($moderateur);

        $this->actingAs($moderateur)
            ->post(route('admin.tickets.respond', $ticket), [
                'reponse_moderateur' => 'Réponse test',
                'statut' => 'resolu',
            ])
            ->assertStatus(403);
    }

    public function test_moderateur_peut_repondre_au_ticket_dun_autre_utilisateur(): void
    {
        $user = $this->createUser();
        $moderateur = $this->createModerator();
        $ticket = $this->createTicket($user);

        $this->actingAs($moderateur)
            ->post(route('admin.tickets.respond', $ticket), [
                'reponse_moderateur' => 'Voici la réponse.',
                'statut' => 'resolu',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'statut' => 'resolu',
            'reponse_moderateur' => 'Voici la réponse.',
        ]);
    }

    public function test_admin_peut_repondre_a_son_propre_ticket(): void
    {
        $admin = $this->createAdmin();
        $ticket = $this->createTicket($admin);

        $this->actingAs($admin)
            ->post(route('admin.tickets.respond', $ticket), [
                'reponse_moderateur' => 'Auto-résolution.',
                'statut' => 'resolu',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'statut' => 'resolu']);
    }

    public function test_proprietaire_peut_fermer_son_ticket(): void
    {
        $user = $this->createUser();
        $ticket = $this->createTicket($user);

        $this->actingAs($user)
            ->post(route('tickets.close', $ticket))
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'statut' => 'ferme']);
    }

    public function test_utilisateur_ne_peut_pas_fermer_le_ticket_dun_autre(): void
    {
        $user = $this->createUser();
        $user2 = $this->createUser();
        $ticket = $this->createTicket($user2);

        $this->actingAs($user)
            ->post(route('tickets.close', $ticket))
            ->assertStatus(403);
    }
}
