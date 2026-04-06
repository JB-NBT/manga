<?php

/**
 * MangaLibrary - Application de gestion de collection de mangas
 *
 * @package    MangaLibrary
 * @author     MangaLibrary Team
 * @copyright  2026 MangaLibrary
 * @license    MIT
 * @version    1.0.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Modèle Manga - Représente un manga dans la collection
 *
 * Ce modèle gère les mangas avec leur statut (privé/public),
 * leurs tomes, avis et demandes de publication.
 *
 * @package    App\Models
 * @author     MangaLibrary Team
 * @version    1.0.0
 *
 * @property int         $id                          Identifiant unique
 * @property int         $user_id                     ID du propriétaire
 * @property string      $titre                       Titre du manga
 * @property string      $auteur                      Auteur du manga
 * @property string|null $description                 Description
 * @property string|null $image_couverture            Chemin de l'image
 * @property int         $nombre_tomes                Nombre total de tomes
 * @property string      $statut                      Statut (en_cours, termine, abandonne)
 * @property int|null    $note                        Note personnelle
 * @property bool        $est_public                  Manga public ou privé
 * @property float|null  $note_moyenne                Moyenne des avis
 * @property int         $nombre_avis                 Nombre d'avis
 * @property \DateTime   $created_at                  Date de création
 * @property \DateTime   $updated_at                  Date de modification
 *
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|Tome[] $tomes
 * @property-read \Illuminate\Database\Eloquent\Collection|Avis[] $avis
 * @property-read PublicationRequest|null $publicationRequest
 */
class Manga extends Model
{
    use HasFactory;

    /**
     * Attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'titre',
        'auteur',
        'description',
        'image_couverture',
        'nombre_tomes',
        'statut',
        'note',
        'est_public',
        'note_moyenne',
        'nombre_avis',
    ];

    /**
     * Définit les casts des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'est_public' => 'boolean',
        'note_moyenne' => 'decimal:1',
    ];

    /**
     * Récupère le propriétaire du manga.
     *
     * @return BelongsTo<User, Manga>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère tous les tomes du manga.
     *
     * @return HasMany<Tome>
     */
    public function tomes(): HasMany
    {
        return $this->hasMany(Tome::class);
    }

    /**
     * Récupère la demande de publication associée.
     *
     * @return HasOne<PublicationRequest>
     */
    public function publicationRequest(): HasOne
    {
        return $this->hasOne(PublicationRequest::class);
    }

    /**
     * Récupère les images de prévisualisation du manga (max 2).
     *
     * @return HasMany<MangaPreview>
     */
    public function previews(): HasMany
    {
        return $this->hasMany(MangaPreview::class)->orderBy('ordre');
    }

    /**
     * Récupère tous les avis du manga.
     *
     * @return HasMany<Avis>
     */
    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class);
    }

    /**
     * Met à jour la note moyenne du manga.
     *
     * Calcule la moyenne des notes des avis et met à jour
     * le nombre total d'avis.
     *
     * @return void
     */
    public function updateNoteMoyenne(): void
    {
        $moyenne = $this->avis()->avg('note');
        $this->note_moyenne = $moyenne ? round($moyenne, 1) : null;
        $this->nombre_avis = $this->avis()->count();
        $this->save();
    }

}
