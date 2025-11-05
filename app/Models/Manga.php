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

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
 * @property \DateTime|null $date_derniere_republication Date de dernière republication
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
        'date_derniere_republication',
    ];

    /**
     * Définit les casts des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'est_public' => 'boolean',
        'note_moyenne' => 'decimal:1',
        'date_derniere_republication' => 'datetime',
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

    /**
     * Vérifie si le manga public a expiré (1 an sans republication).
     *
     * Un manga expire s'il est public et n'a pas été republié
     * depuis plus d'un an.
     *
     * @return bool True si le manga a expiré
     */
    public function isExpired(): bool
    {
        if (!$this->est_public) {
            return false;
        }

        if ($this->date_derniere_republication) {
            return $this->date_derniere_republication->addYear()->isPast();
        }

        return $this->created_at->addYear()->isPast();
    }

    /**
     * Retire le manga de la publication.
     *
     * Utilisé pour la protection des droits d'auteur.
     *
     * @return void
     */
    public function unpublish(): void
    {
        $this->update([
            'est_public' => false,
        ]);
    }

    /**
     * Republie le manga (modérateur/admin uniquement).
     *
     * Remet le manga en public et met à jour la date de republication.
     *
     * @return void
     */
    public function republish(): void
    {
        $this->update([
            'est_public' => true,
            'date_derniere_republication' => now(),
        ]);
    }

    /**
     * Scope pour récupérer les mangas expirés.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('est_public', true)
            ->where(function ($q) {
                $q->whereNull('date_derniere_republication')
                  ->where('created_at', '<', Carbon::now()->subYear())
                  ->orWhere('date_derniere_republication', '<', Carbon::now()->subYear());
            });
    }

    /**
     * Scope pour récupérer les mangas bientôt expirés (30 jours).
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeExpiringSoon(Builder $query): Builder
    {
        $dateLimit = Carbon::now()->subDays(335);

        return $query->where('est_public', true)
            ->where(function ($q) use ($dateLimit) {
                $q->whereNull('date_derniere_republication')
                  ->where('created_at', '<', $dateLimit)
                  ->orWhere('date_derniere_republication', '<', $dateLimit);
            });
    }
}
