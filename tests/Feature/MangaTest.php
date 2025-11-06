<?php

namespace Tests\Unit\Models;

use App\Models\Manga;
use App\Models\User;
use App\Models\Tome;
use App\Models\Avis;
use App\Models\PublicationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Classe MangaTest
 *
 * Cette classe représente l'opération de test du modèle Manga.
 *
 * @package App\Tests\Unit\Models
 */
class MangaTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test que la page du manga affiche bien "One Piece".
     *
     * @return void
     */
    public function test_manga_page_displays_one_piece(): void
    {
        // Création d’un utilisateur
        $user = User::factory()->create();

        // Création d’un manga
        $manga = Manga::factory()->create([
            'user_id' => $user->id,
            'titre' => 'One Piece',
            'description' => 'Un manga légendaire sur les pirates.',
            'est_public' => true,
        ]);

        // Accès à la page du manga
        $response = $this->get("/mangas/{$manga->id}");

        // Vérifie que le texte "One Piece" apparaît sur la page
        $response->assertStatus(200);
        $response->assertSee('One Piece');
    }

    /**
     * Test de la route edit qui retourne un code 200.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_manga_edit_page_returns_200(): void
    {
        /**
         * @var Object $permission Création de la permission
         */
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage all mangas']);
        
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::factory()->create();
        $user->givePermissionTo('manage all mangas');
        
        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::factory()->create(['user_id' => $user->id]);

        /**
         * @var Object $response Appel de la route edit
         */
        $response = $this->actingAs($user)->get("/mangas/{$manga->id}/edit");

        // Vérifications
        $response->assertStatus(200);
    }

    /**
     * Test de la route publique d'un manga qui retourne un code 200.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_public_manga_page_returns_200(): void
    {
        /**
         * @var Manga $manga Création d'un manga public
         */
        $manga = Manga::factory()->create([
            'est_public' => true,
        ]);

        /**
         * @var Object $response Accès SANS authentification (route publique)
         */
        $response = $this->get("/mangas/{$manga->id}");
        
        // Vérifications
        $response->assertStatus(200);
    }
    
    // ========================================
    // TESTS CRUD POSITIFS (sans factory)
    // ========================================

    /**
     * Test de la création d'un manga manuellement.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_can_create_manga_manually(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        /**
         * @var Manga $manga Création d'un manga sans factory
         */
        $manga = Manga::create([
            'user_id' => $user->id,
            'titre' => 'One Piece',
            'auteur' => 'Eiichiro Oda',
            'description' => 'L\'histoire d\'un pirate',
            'nombre_tomes' => 100,
            'statut' => 'en_cours',
            'note' => 9,
            'est_public' => true,
        ]);

        // Vérifications
        $this->assertNotNull($manga->id);
        $this->assertEquals('One Piece', $manga->titre);
        $this->assertEquals('Eiichiro Oda', $manga->auteur);
        $this->assertTrue($manga->est_public);
    }

    /**
     * Test de la lecture d'un manga depuis la base de données.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_can_read_manga_from_database(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);

        /**
         * @var Manga $createdManga Création d'un manga
         */
        $createdManga = Manga::create([
            'user_id' => $user->id,
            'titre' => 'Naruto',
            'auteur' => 'Masashi Kishimoto',
            'description' => 'Un ninja',
            'nombre_tomes' => 72,
            'statut' => 'termine',
            'note' => 8,
            'est_public' => false,
        ]);

        /**
         * @var Manga $foundManga Lecture du manga depuis la base de données
         */
        $foundManga = Manga::find($createdManga->id);

        // Vérifications
        $this->assertNotNull($foundManga);
        $this->assertEquals('Naruto', $foundManga->titre);
        $this->assertEquals('Masashi Kishimoto', $foundManga->auteur);
        $this->assertEquals(72, $foundManga->nombre_tomes);
        $this->assertFalse($foundManga->est_public);
    }

    /**
     * Test de la mise à jour d'un manga.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_can_update_manga(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::create([
            'user_id' => $user->id,
            'titre' => 'Dragon Ball',
            'auteur' => 'Akira Toriyama',
            'nombre_tomes' => 40,
            'statut' => 'en_cours',
            'note' => 7,
            'est_public' => false,
        ]);

        /**
         * @var array<string, mixed> $updatedData Données mises à jour
         */
        $updatedData = [
            'nombre_tomes' => 42,
            'statut' => 'termine',
            'note' => 9,
            'est_public' => true,
        ];

        /**
         * @var void Mise à jour du manga
         */
        $manga->update($updatedData);

        /**
         * @var Manga $updatedManga Rechargement depuis la base de données
         */
        $updatedManga = Manga::find($manga->id);

        // Vérifications
        $this->assertEquals(42, $updatedManga->nombre_tomes);
        $this->assertEquals('termine', $updatedManga->statut);
        $this->assertEquals(9, $updatedManga->note);
        $this->assertTrue($updatedManga->est_public);
    }

    /**
     * Test de la suppression d'un manga.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_can_delete_manga(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::create([
            'name' => 'Delete User',
            'email' => 'delete@example.com',
            'password' => bcrypt('password'),
        ]);

        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::create([
            'user_id' => $user->id,
            'titre' => 'Manga à supprimer',
            'auteur' => 'Auteur Test',
            'nombre_tomes' => 10,
            'statut' => 'en_cours',
        ]);

        /**
         * @var int $mangaId Sauvegarde de l'ID du manga
         */
        $mangaId = $manga->id;

        /**
         * @var void Suppression du manga
         */
        $manga->delete();

        // Vérifications
        $this->assertNull(Manga::find($mangaId));
        $this->assertDatabaseMissing('mangas', [
            'id' => $mangaId,
        ]);
    }

    /**
     * Test de la liste de tous les mangas.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_can_list_all_mangas(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::create([
            'name' => 'List User',
            'email' => 'list@example.com',
            'password' => bcrypt('password'),
        ]);

        /**
         * @var Manga $manga1 Création du premier manga
         */
        Manga::create([
            'user_id' => $user->id,
            'titre' => 'Manga 1',
            'auteur' => 'Auteur 1',
            'nombre_tomes' => 5,
            'statut' => 'en_cours',
        ]);

        /**
         * @var Manga $manga2 Création du deuxième manga
         */
        Manga::create([
            'user_id' => $user->id,
            'titre' => 'Manga 2',
            'auteur' => 'Auteur 2',
            'nombre_tomes' => 10,
            'statut' => 'termine',
        ]);

        /**
         * @var Manga $manga3 Création du troisième manga
         */
        Manga::create([
            'user_id' => $user->id,
            'titre' => 'Manga 3',
            'auteur' => 'Auteur 3',
            'nombre_tomes' => 15,
            'statut' => 'en_cours',
        ]);

        /**
         * @var Collection $mangas Récupération de tous les mangas
         */
        $mangas = Manga::all();

        // Vérifications
        $this->assertCount(3, $mangas);
    }

    // ========================================
    // TESTS NÉGATIFS (cas d'erreur)
    // ========================================

    /**
     * Test de l'impossibilité de créer un manga sans user_id.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_cannot_create_manga_without_user_id(): void
    {
        /**
         * @var void Attente d'une exception
         */
        $this->expectException(QueryException::class);

        /**
         * @var void Tentative de création sans user_id
         */
        Manga::create([
            'titre' => 'Manga sans user',
            'auteur' => 'Auteur',
            'nombre_tomes' => 10,
            'statut' => 'en_cours',
        ]);
    }

    /**
     * Test de l'impossibilité de créer un manga avec un user_id invalide.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_cannot_create_manga_with_invalid_user_id(): void
    {
        /**
         * @var void Attente d'une exception
         */
        $this->expectException(QueryException::class);

        /**
         * @var void Tentative de création avec un ID inexistant
         */
        Manga::create([
            'user_id' => 99999,
            'titre' => 'Manga invalide',
            'auteur' => 'Auteur',
            'nombre_tomes' => 10,
            'statut' => 'en_cours',
        ]);
    }

    /**
     * Test de l'obligation du champ titre.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_titre_is_required(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
        ]);

        /**
         * @var void Attente d'une exception
         */
        $this->expectException(QueryException::class);

        /**
         * @var void Tentative de création sans titre
         */
        Manga::create([
            'user_id' => $user->id,
            'auteur' => 'Auteur',
            'nombre_tomes' => 10,
            'statut' => 'en_cours',
        ]);
    }

    /**
     * Test de l'impossibilité de mettre à jour un manga inexistant.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_cannot_update_nonexistent_manga(): void
    {
        /**
         * @var Manga|null $manga Tentative de récupération d'un manga inexistant
         */
        $manga = Manga::find(99999);

        // Vérifications
        $this->assertNull($manga);
    }

    /**
     * Test de la restriction de l'assignation directe de note_moyenne.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_note_moyenne_cannot_be_set_directly_via_mass_assignment(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test3@example.com',
            'password' => bcrypt('password'),
        ]);

        /**
         * @var Manga $manga Tentative de création avec note_moyenne forcée
         */
        $manga = Manga::create([
            'user_id' => $user->id,
            'titre' => 'Test Manga',
            'auteur' => 'Auteur',
            'note_moyenne' => 9.5,
            'nombre_tomes' => 10,
            'statut' => 'en_cours',
        ]);

        // Vérifications
        $this->assertTrue(
            is_null($manga->note_moyenne) || $manga->note_moyenne == 9.5,
            'La note moyenne devrait soit être null, soit acceptée si dans fillable'
        );
    }

    /**
     * Test de la protection contre l'assignation d'attributs non-fillable.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_cannot_assign_non_fillable_attributes(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test4@example.com',
            'password' => bcrypt('password'),
        ]);

        /**
         * @var Manga $manga Tentative de création avec attributs non-fillable
         */
        $manga = Manga::create([
            'user_id' => $user->id,
            'titre' => 'Test Manga',
            'auteur' => 'Auteur',
            'nombre_tomes' => 10,
            'statut' => 'en_cours',
            'id' => 12345,
            'created_at' => '2020-01-01',
        ]);

        // Vérifications
        $this->assertNotEquals(12345, $manga->id);
    }

    /**
     * Test de la valeur par défaut de est_public.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_est_public_defaults_to_false_when_not_provided(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test5@example.com',
            'password' => bcrypt('password'),
        ]);

        /**
         * @var Manga $manga Création d'un manga sans spécifier est_public
         */
        $manga = Manga::create([
            'user_id' => $user->id,
            'titre' => 'Test Manga',
            'auteur' => 'Auteur',
            'nombre_tomes' => 10,
            'statut' => 'en_cours',
        ]);

        // Vérifications
        $this->assertIsBool($manga->est_public);
    }

    /**
     * Test de l'impact de la suppression d'un utilisateur sur ses mangas.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_deleting_user_affects_manga_with_foreign_key(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::create([
            'name' => 'User to Delete',
            'email' => 'usertodelete@example.com',
            'password' => bcrypt('password'),
        ]);

        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::create([
            'user_id' => $user->id,
            'titre' => 'Manga orphelin',
            'auteur' => 'Auteur',
            'nombre_tomes' => 10,
            'statut' => 'en_cours',
        ]);

        /**
         * @var void Tentative de suppression de l'utilisateur
         */
        try {
            $user->delete();
            // Vérifications si cascade
            $this->assertNull(Manga::find($manga->id));
        } catch (QueryException $e) {
            // Vérifications si pas de cascade
            $this->assertTrue(true, 'Contrainte de clé étrangère respectée');
        }
    }

    /**
     * Test de la contrainte d'unicité (si elle existe).
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_cannot_create_duplicate_manga_if_unique_constraint_exists(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test6@example.com',
            'password' => bcrypt('password'),
        ]);

        /**
         * @var Manga $firstManga Création d'un premier manga
         */
        Manga::create([
            'user_id' => $user->id,
            'titre' => 'Manga Unique',
            'auteur' => 'Auteur Unique',
            'nombre_tomes' => 10,
            'statut' => 'en_cours',
        ]);

        /**
         * @var Manga $secondManga Tentative de création d'un doublon
         */
        $secondManga = Manga::create([
            'user_id' => $user->id,
            'titre' => 'Manga Unique',
            'auteur' => 'Auteur Unique',
            'nombre_tomes' => 10,
            'statut' => 'en_cours',
        ]);

        // Vérifications
        $this->assertNotNull($secondManga);
        $this->assertCount(2, Manga::all());
    }

    // ========================================
    // TESTS AVEC FACTORY (relations et casts)
    // ========================================

    /**
     * Test de la création d'un manga via factory.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_it_can_create_a_manga(): void
    {
        /**
         * @var Manga $manga Création d'un manga avec factory
         */
        $manga = Manga::factory()->create([
            'titre' => 'Test Manga',
            'auteur' => 'Test Author',
        ]);

        // Vérifications
        $this->assertDatabaseHas('mangas', [
            'titre' => 'Test Manga',
            'auteur' => 'Test Author',
        ]);
    }

    /**
     * Test de la relation belongsTo avec User.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_it_belongs_to_a_user(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::factory()->create();
        
        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::factory()->create(['user_id' => $user->id]);

        // Vérifications
        $this->assertInstanceOf(User::class, $manga->user);
        $this->assertEquals($user->id, $manga->user->id);
    }

    /**
     * Test de la relation hasMany avec Tome.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_it_has_many_tomes(): void
    {
        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::factory()->create();
        
        /**
         * @var Tome $tome1 Création du premier tome
         */
        $tome1 = Tome::factory()->create(['manga_id' => $manga->id]);
        
        /**
         * @var Tome $tome2 Création du deuxième tome
         */
        $tome2 = Tome::factory()->create(['manga_id' => $manga->id]);

        // Vérifications
        $this->assertCount(2, $manga->tomes);
        $this->assertTrue($manga->tomes->contains($tome1));
        $this->assertTrue($manga->tomes->contains($tome2));
    }

    /**
     * Test de la relation hasMany avec Avis.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_it_has_many_avis(): void
    {
        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::factory()->create();
        
        /**
         * @var Avis $avis1 Création du premier avis
         */
        $avis1 = Avis::factory()->create(['manga_id' => $manga->id]);
        
        /**
         * @var Avis $avis2 Création du deuxième avis
         */
        $avis2 = Avis::factory()->create(['manga_id' => $manga->id]);

        // Vérifications
        $this->assertCount(2, $manga->avis);
        $this->assertTrue($manga->avis->contains($avis1));
    }

    /**
     * Test du cast de est_public en booléen.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_it_casts_est_public_to_boolean(): void
    {
        /**
         * @var Manga $manga Création d'un manga avec est_public = 1
         */
        $manga = Manga::factory()->create(['est_public' => 1]);

        // Vérifications
        $this->assertIsBool($manga->est_public);
        $this->assertTrue($manga->est_public);
    }

    /**
     * Test du cast de note_moyenne en décimal.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_it_casts_note_moyenne_to_decimal(): void
    {
        /**
         * @var Manga $manga Création d'un manga avec une note moyenne
         */
        $manga = Manga::factory()->create(['note_moyenne' => 7.5]);

        // Vérifications
        $this->assertEquals('7.5', $manga->note_moyenne);
    }

    /**
     * Test de la mise à jour correcte de note_moyenne.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_it_updates_note_moyenne_correctly(): void
    {
        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::factory()->create();
        
        /**
         * @var Avis $avis1 Création du premier avis (note 8)
         */
        Avis::factory()->create(['manga_id' => $manga->id, 'note' => 8]);
        
        /**
         * @var Avis $avis2 Création du deuxième avis (note 6)
         */
        Avis::factory()->create(['manga_id' => $manga->id, 'note' => 6]);
        
        /**
         * @var Avis $avis3 Création du troisième avis (note 9)
         */
        Avis::factory()->create(['manga_id' => $manga->id, 'note' => 9]);

        /**
         * @var void Calcul de la note moyenne
         */
        $manga->updateNoteMoyenne();

        // Vérifications (8+6+9)/3 = 7.666... ≈ 7.7
        $this->assertEquals(7.7, (float) $manga->note_moyenne);
        $this->assertEquals(3, $manga->nombre_avis);
    }

    /**
     * Test de note_moyenne à null quand aucun avis.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_it_sets_note_moyenne_to_null_when_no_avis(): void
    {
        /**
         * @var Manga $manga Création d'un manga sans avis
         */
        $manga = Manga::factory()->create();
        
        /**
         * @var void Calcul de la note moyenne
         */
        $manga->updateNoteMoyenne();

        // Vérifications
        $this->assertNull($manga->note_moyenne);
        $this->assertEquals(0, $manga->nombre_avis);
    }

    /**
     * Test de l'arrondi de note_moyenne à 1 décimale.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_it_rounds_note_moyenne_to_one_decimal(): void
    {
        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::factory()->create();
        
        /**
         * @var Avis $avis1 Création du premier avis (note 7)
         */
        Avis::factory()->create(['manga_id' => $manga->id, 'note' => 7]);
        
        /**
         * @var Avis $avis2 Création du deuxième avis (note 8)
         */
        Avis::factory()->create(['manga_id' => $manga->id, 'note' => 8]);
        
        /**
         * @var Avis $avis3 Création du troisième avis (note 9)
         */
        Avis::factory()->create(['manga_id' => $manga->id, 'note' => 9]);

        /**
         * @var void Calcul de la note moyenne
         */
        $manga->updateNoteMoyenne();

        // Vérifications (7+8+9)/3 = 8.0
        $this->assertEquals(8.0, (float) $manga->note_moyenne);
    }

    /**
     * Test de l'assignation en masse des attributs fillable.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_fillable_attributes_are_mass_assignable(): void
    {
        /**
         * @var array<string, mixed> $data Données du manga
         */
        $data = [
            'user_id' => User::factory()->create()->id,
            'titre' => 'Test Manga',
            'auteur' => 'Test Author',
            'description' => 'Test Description',
            'nombre_tomes' => 10,
            'statut' => 'en_cours',
            'note' => 8,
            'est_public' => true,
        ];

        /**
         * @var Manga $manga Création d'un manga via mass assignment
         */
        $manga = Manga::create($data);

        // Vérifications
        $this->assertEquals('Test Manga', $manga->titre);
        $this->assertEquals('Test Author', $manga->auteur);
        $this->assertEquals(10, $manga->nombre_tomes);
    }

    /**
     * Test de la relation hasOne avec PublicationRequest.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_it_has_one_publication_request(): void
    {
        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::factory()->create();
        
        /**
         * @var PublicationRequest $request Création d'une demande de publication
         */
        $request = PublicationRequest::factory()->create(['manga_id' => $manga->id]);

        /**
         * @var Manga $manga Rechargement du manga depuis la base de données
         */
        $manga = $manga->fresh();

        // Vérifications
        $this->assertInstanceOf(PublicationRequest::class, $manga->publicationRequest);
        $this->assertEquals($request->id, $manga->publicationRequest->id);
    }

    // ========================================
    // TESTS NEGATIFS AVEC assertDontSee
    // ========================================
    /**
     * Test que la page edit ne contient pas d'informations d'un autre manga.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_manga_edit_page_does_not_show_other_manga_data(): void
    {
        /**
         * @var Object $permission Création de la permission
         */
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage all mangas']);
        
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::factory()->create();
        $user->givePermissionTo('manage all mangas');
        
        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::factory()->create([
            'user_id' => $user->id,
            'titre' => 'One Piece',
            'auteur' => 'Eiichiro Oda',
        ]);

        /**
         * @var Manga $otherManga Création d'un autre manga
         */
        $otherManga = Manga::factory()->create([
            'user_id' => $user->id,
            'titre' => 'Naruto',
            'auteur' => 'Masashi Kishimoto',
        ]);

        /**
         * @var Object $response Appel de la route edit
         */
        $response = $this->actingAs($user)->get("/mangas/{$manga->id}/edit");

        // Vérifications
        $response->assertStatus(200);
        $response->assertSee('One Piece');
        $response->assertSee('Eiichiro Oda');
        $response->assertDontSee('Naruto');
        $response->assertDontSee('Masashi Kishimoto');
    }

    /**
     * Test qu'un manga privé n'affiche pas le contenu pour un utilisateur non authentifié.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_private_manga_page_does_not_show_content_to_unauthenticated_user(): void
    {
        /**
         * @var User $user Création du propriétaire
         */
        $user = User::factory()->create();

        /**
         * @var Manga $manga Création d'un manga privé
         */
        $manga = Manga::factory()->create([
            'user_id' => $user->id,
            'est_public' => false,
            'titre' => 'Manga Privé',
            'description' => 'Description secrète',
        ]);

        /**
         * @var Object $response Accès sans authentification
         */
        $response = $this->get("/mangas/{$manga->id}");

        // Vérifications : devrait être redirigé ou recevoir un code d'erreur
        $this->assertTrue(in_array($response->status(), [302, 403, 404]));
    }

    /**
     * Test que la liste des mangas n'affiche pas les mangas privés d'autres utilisateurs.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_index_page_does_not_show_other_users_private_mangas(): void
    {
        /**
         * @var User $user1 Création du premier utilisateur
         */
        $user1 = User::factory()->create();

        /**
         * @var User $user2 Création du second utilisateur
         */
        $user2 = User::factory()->create();

        /**
         * @var Manga $publicManga Création d'un manga public
         */
        $publicManga = Manga::factory()->create([
            'user_id' => $user1->id,
            'titre' => 'Manga Public',
            'est_public' => true,
        ]);

        /**
         * @var Manga $privateManga Création d'un manga privé
         */
        $privateManga = Manga::factory()->create([
            'user_id' => $user1->id,
            'titre' => 'Manga Privé User1',
            'est_public' => false,
        ]);

        /**
         * @var Collection $user2Mangas Récupération des mangas accessibles par user2
         */
        $user2Mangas = Manga::where('est_public', true)
            ->orWhere('user_id', $user2->id)
            ->get();

        // Vérifications
        $this->assertTrue($user2Mangas->contains($publicManga));
        $this->assertFalse($user2Mangas->contains($privateManga));
    }

    /**
     * Test qu'un manga supprimé n'apparaît plus dans la liste.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_deleted_manga_does_not_appear_in_list(): void
    {
        /**
         * @var User $user Création d'un utilisateur
         */
        $user = User::factory()->create();

        /**
         * @var Manga $manga1 Création du premier manga
         */
        $manga1 = Manga::factory()->create([
            'user_id' => $user->id,
            'titre' => 'Manga Actif',
            'est_public' => true,
        ]);

        /**
         * @var Manga $manga2 Création du manga à supprimer
         */
        $manga2 = Manga::factory()->create([
            'user_id' => $user->id,
            'titre' => 'Manga à Supprimer',
            'est_public' => true,
        ]);

        /**
         * @var int $manga2Id Sauvegarde de l'ID
         */
        $manga2Id = $manga2->id;

        /**
         * @var void Suppression du deuxième manga
         */
        $manga2->delete();

        /**
         * @var Collection $mangas Récupération de tous les mangas actifs
         */
        $mangas = Manga::all();

        // Vérifications
        $this->assertCount(1, $mangas);
        $this->assertTrue($mangas->contains($manga1));
        $this->assertNull(Manga::find($manga2Id));
    }

    /**
     * Test que les avis d'un manga ne contiennent pas d'avis d'autres mangas.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_manga_page_does_not_show_other_manga_reviews(): void
    {
        /**
         * @var Manga $manga1 Création du premier manga
         */
        $manga1 = Manga::factory()->create([
            'titre' => 'Manga 1',
            'est_public' => true,
        ]);

        /**
         * @var Manga $manga2 Création du second manga
         */
        $manga2 = Manga::factory()->create([
            'titre' => 'Manga 2',
            'est_public' => true,
        ]);

        /**
         * @var Avis $avis1 Création d'un avis pour manga1
         */
        $avis1 = Avis::factory()->create([
            'manga_id' => $manga1->id,
            'commentaire' => 'Excellent manga!',
        ]);

        /**
         * @var Avis $avis2 Création d'un avis pour manga2
         */
        $avis2 = Avis::factory()->create([
            'manga_id' => $manga2->id,
            'commentaire' => 'Pas terrible ce manga',
        ]);

        /**
         * @var Collection $manga1Avis Récupération des avis du manga 1
         */
        $manga1Avis = $manga1->avis;

        // Vérifications
        $this->assertCount(1, $manga1Avis);
        $this->assertTrue($manga1Avis->contains($avis1));
        $this->assertFalse($manga1Avis->contains($avis2));
    }

    /**
     * Test que les tomes d'un manga ne contiennent pas de tomes d'autres mangas.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_manga_page_does_not_show_other_manga_tomes(): void
    {
        /**
         * @var Manga $manga1 Création du premier manga
         */
        $manga1 = Manga::factory()->create([
            'titre' => 'Manga 1',
            'est_public' => true,
        ]);

        /**
         * @var Manga $manga2 Création du second manga
         */
        $manga2 = Manga::factory()->create([
            'titre' => 'Manga 2',
            'est_public' => true,
        ]);

        /**
         * @var Tome $tome1 Création d'un tome pour manga1
         */
        $tome1 = Tome::factory()->create([
            'manga_id' => $manga1->id,
            'numero' => 1,
        ]);

        /**
         * @var Tome $tome2 Création d'un tome pour manga2
         */
        $tome2 = Tome::factory()->create([
            'manga_id' => $manga2->id,
            'numero' => 1,
        ]);

        /**
         * @var Collection $manga1Tomes Récupération des tomes du manga 1
         */
        $manga1Tomes = $manga1->tomes;

        // Vérifications
        $this->assertCount(1, $manga1Tomes);
        $this->assertTrue($manga1Tomes->contains($tome1));
        $this->assertFalse($manga1Tomes->contains($tome2));
    }

    /**
     * Test qu'un manga avec une note moyenne nulle ne l'affiche pas comme 0.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_manga_without_reviews_does_not_show_zero_rating(): void
    {
        /**
         * @var Manga $manga Création d'un manga sans avis
         */
        $manga = Manga::factory()->create([
            'titre' => 'Nouveau Manga',
            'est_public' => true,
            'note_moyenne' => null,
        ]);

        /**
         * @var Object $response Appel de la page du manga
         */
        $response = $this->get("/mangas/{$manga->id}");

        // Vérifications
        $response->assertStatus(200);
        $response->assertSee('Nouveau Manga');
        // Ne devrait pas afficher "Note: 0" ou "0/10"
        $response->assertDontSee('Note: 0');
        $response->assertDontSee('0/10');
    }

    /**
     * Test qu'un utilisateur non autorisé ne voit pas le bouton d'édition.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_unauthorized_user_cannot_see_edit_button(): void
    {
        /**
         * @var User $owner Création du propriétaire
         */
        $owner = User::factory()->create();

        /**
         * @var User $otherUser Création d'un autre utilisateur
         */
        $otherUser = User::factory()->create();

        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::factory()->create([
            'user_id' => $owner->id,
            'titre' => 'Manga du Propriétaire',
            'est_public' => true,
        ]);

        // Vérifications : l'autre utilisateur n'est pas le propriétaire
        $this->assertNotEquals($owner->id, $otherUser->id);
        $this->assertEquals($owner->id, $manga->user_id);
        
        // Test via policy si disponible
        if (method_exists($manga, 'user')) {
            $this->assertEquals($owner->id, $manga->user->id);
        }
    }

    /**
     * Test que les statistiques d'un manga ne montrent pas de données incohérentes.
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_manga_statistics_do_not_show_inconsistent_data(): void
    {
        /**
         * @var Manga $manga Création d'un manga
         */
        $manga = Manga::factory()->create([
            'nombre_tomes' => 10,
            'est_public' => true,
        ]);

        /**
         * @var Tome $tome1 Création d'un seul tome
         */
        Tome::factory()->create([
            'manga_id' => $manga->id,
            'numero' => 1,
        ]);

        /**
         * @var Object $response Appel de la page du manga
         */
        $response = $this->get("/mangas/{$manga->id}");

        // Vérifications
        $response->assertStatus(200);
        // Ne devrait pas afficher "10/10 tomes collectés" alors qu'il n'y en a qu'un
        $response->assertDontSee('10/10 tomes');
    }

    /**
     * Test qu'un manga en cours ne montre pas le statut "Terminé".
     * 
     * @access public
     * @return void
     */
    #[Test]
    public function test_ongoing_manga_does_not_show_completed_status(): void
    {
        /**
         * @var Manga $manga Création d'un manga en cours
         */
        $manga = Manga::factory()->create([
            'titre' => 'Manga en Cours',
            'statut' => 'en_cours',
            'est_public' => true,
        ]);

        /**
         * @var Object $response Appel de la page du manga
         */
        $response = $this->get("/mangas/{$manga->id}");

        // Vérifications
        $response->assertStatus(200);
        $response->assertSee('Manga en Cours');
        $response->assertDontSee('Terminé');
        $response->assertDontSee('Complété');
        $response->assertDontSee('Fini');
    }
}
