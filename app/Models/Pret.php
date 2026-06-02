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

use App\Enums\StatutEmprunt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Pret - Représente un prêt de tome entre deux utilisateurs
 *
 * Ce modèle gère les prêts de tomes entre utilisateurs avec tracking
 * des dates d'emprunt, retour prévue et effective.
 *
 * @package    App\Models
 * @author     MangaLibrary Team
 * @version    1.0.0
 *
 * @property int              $id                    Identifiant unique
 * @property int              $preteur_id            ID de l'utilisateur qui prête
 * @property int              $emprunteur_id         ID de l'utilisateur qui emprunte
 * @property int              $tome_id               ID du tome prêté
 * @property string           $statut                Statut du prêt (demande, accepte, en_cours, restitue, refuse)
 * @property \DateTime        $date_demande          Date de la demande
 * @property \DateTime|null   $date_emprunt          Date d'acceptation du prêt
 * @property \DateTime|null   $date_retour_prevue    Date prévue de retour
 * @property \DateTime|null   $date_retour_effective Date effective de retour
 * @property string|null      $motif_refus           Motif du refus si applicable
 * @property \DateTime        $created_at            Date de création
 * @property \DateTime        $updated_at            Date de modification
 *
 * @property-read User $preteur
 * @property-read User $emprunteur
 * @property-read Tome $tome
 */
class Pret extends Model
{
    use HasFactory;

    /**
     * Attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'preteur_id',
        'emprunteur_id',
        'tome_id',
        'statut',
        'date_demande',
        'date_emprunt',
        'date_retour_prevue',
        'date_retour_effective',
        'motif_refus',
    ];

    /**
     * Définit les casts des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_demande' => 'date',
        'date_emprunt' => 'date',
        'date_retour_prevue' => 'date',
        'date_retour_effective' => 'date',
        'statut' => StatutEmprunt::class,
    ];

    /**
     * Récupère l'utilisateur qui prête.
     *
     * @return BelongsTo<User, Pret>
     */
    public function preteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'preteur_id');
    }

    /**
     * Récupère l'utilisateur qui emprunte.
     *
     * @return BelongsTo<User, Pret>
     */
    public function emprunteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emprunteur_id');
    }

    /**
     * Récupère le tome prêté.
     *
     * @return BelongsTo<Tome, Pret>
     */
    public function tome(): BelongsTo
    {
        return $this->belongsTo(Tome::class);
    }

    /**
     * Vérifie si le prêt est en attente d'acceptation.
     */
    public function isPending(): bool
    {
        return $this->statut === StatutEmprunt::DEMANDE;
    }

    /**
     * Vérifie si le prêt est actif (en cours).
     */
    public function isActive(): bool
    {
        return $this->statut === StatutEmprunt::EN_COURS;
    }

    /**
     * Vérifie si le prêt est terminé (restitué).
     */
    public function isCompleted(): bool
    {
        return $this->statut === StatutEmprunt::RESTITUE;
    }

    /**
     * Vérifie si le prêt a été refusé.
     */
    public function isRefused(): bool
    {
        return $this->statut === StatutEmprunt::REFUSE;
    }
}
