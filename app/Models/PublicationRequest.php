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

/**
 * Modèle PublicationRequest - Représente une demande de publication
 *
 * Ce modèle gère les demandes de publication des mangas privés
 * vers la bibliothèque publique. Les demandes sont validées par
 * les modérateurs.
 *
 * @package    App\Models
 * @author     MangaLibrary Team
 * @version    1.0.0
 *
 * @property int              $id                   Identifiant unique
 * @property int              $manga_id             ID du manga concerné
 * @property int              $user_id              ID de l'utilisateur demandeur
 * @property string           $statut               Statut (en_attente, approuve, refuse)
 * @property string|null      $message_utilisateur  Message de l'utilisateur
 * @property string|null      $message_admin        Réponse du modérateur
 * @property \DateTime|null   $date_demande         Date de la demande
 * @property \DateTime|null   $date_traitement      Date de traitement
 * @property \DateTime        $created_at           Date de création
 * @property \DateTime        $updated_at           Date de modification
 *
 * @property-read Manga $manga
 * @property-read User  $user
 */
class PublicationRequest extends Model
{
    use HasFactory;

    /**
     * Statuts disponibles pour les demandes de publication.
     */
    public const STATUTS = [
        'en_attente' => 'En attente',
        'approuve' => 'Approuvé',
        'refuse' => 'Refusé',
    ];

    /**
     * Attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'manga_id',
        'user_id',
        'statut',
        'message_utilisateur',
        'message_admin',
        'date_traitement',
    ];

    /**
     * Définit les casts des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_demande' => 'datetime',
        'date_traitement' => 'datetime',
    ];

    /**
     * Récupère le manga associé à la demande.
     *
     * @return BelongsTo<Manga, PublicationRequest>
     */
    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }

    /**
     * Récupère l'utilisateur qui a fait la demande.
     *
     * @return BelongsTo<User, PublicationRequest>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vérifie si la demande est en attente.
     *
     * @return bool
     */
    public function estEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    /**
     * Vérifie si la demande a été approuvée.
     *
     * @return bool
     */
    public function estApprouvee(): bool
    {
        return $this->statut === 'approuve';
    }

    /**
     * Vérifie si la demande a été refusée.
     *
     * @return bool
     */
    public function estRefusee(): bool
    {
        return $this->statut === 'refuse';
    }
}
