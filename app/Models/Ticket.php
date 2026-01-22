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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Ticket - Représente un ticket de support
 *
 * Ce modèle gère les tickets de support créés par les utilisateurs
 * pour signaler des bugs, demander de l'aide ou faire des suggestions.
 *
 * @package    App\Models
 * @author     MangaLibrary Team
 * @version    1.0.0
 *
 * @property int              $id                   Identifiant unique
 * @property int              $user_id              ID de l'auteur du ticket
 * @property int|null         $assigned_to          ID du modérateur assigné
 * @property string           $sujet                Sujet du ticket
 * @property string           $description          Description détaillée
 * @property string           $categorie            Catégorie (bug, contenu, compte, suggestion, autre)
 * @property string           $priorite             Priorité (basse, normale, haute, urgente)
 * @property string           $statut               Statut (ouvert, en_cours, resolu, ferme)
 * @property string|null      $reponse_moderateur   Réponse du modérateur
 * @property \DateTime|null   $date_resolution      Date de résolution
 * @property \DateTime        $created_at           Date de création
 * @property \DateTime        $updated_at           Date de modification
 *
 * @property-read User      $user
 * @property-read User|null $assignedTo
 */
class Ticket extends Model
{
    use HasFactory;

    /**
     * Catégories disponibles pour les tickets.
     */
    public const CATEGORIES = [
        'bug' => 'Bug technique',
        'contenu' => 'Problème de contenu',
        'compte' => 'Problème de compte',
        'suggestion' => 'Suggestion',
        'autre' => 'Autre',
    ];

    /**
     * Priorités disponibles pour les tickets.
     */
    public const PRIORITES = [
        'basse' => 'Basse',
        'normale' => 'Normale',
        'haute' => 'Haute',
        'urgente' => 'Urgente',
    ];

    /**
     * Statuts disponibles pour les tickets.
     */
    public const STATUTS = [
        'ouvert' => 'Ouvert',
        'en_cours' => 'En cours',
        'resolu' => 'Résolu',
        'ferme' => 'Fermé',
    ];

    /**
     * Attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'assigned_to',
        'sujet',
        'description',
        'categorie',
        'priorite',
        'statut',
        'reponse_moderateur',
        'date_resolution',
    ];

    /**
     * Définit les casts des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_resolution' => 'datetime',
    ];

    /**
     * Récupère l'auteur du ticket.
     *
     * @return BelongsTo<User, Ticket>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère le modérateur assigné au ticket.
     *
     * @return BelongsTo<User, Ticket>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope pour récupérer les tickets ouverts.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeOuverts(Builder $query): Builder
    {
        return $query->where('statut', 'ouvert');
    }

    /**
     * Scope pour récupérer les tickets en cours.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeEnCours(Builder $query): Builder
    {
        return $query->where('statut', 'en_cours');
    }

    /**
     * Scope pour récupérer les tickets non résolus.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeNonResolus(Builder $query): Builder
    {
        return $query->whereIn('statut', ['ouvert', 'en_cours']);
    }

    /**
     * Vérifie si le ticket est ouvert.
     *
     * @return bool
     */
    public function estOuvert(): bool
    {
        return $this->statut === 'ouvert';
    }

    /**
     * Vérifie si le ticket est résolu ou fermé.
     *
     * @return bool
     */
    public function estResolu(): bool
    {
        return in_array($this->statut, ['resolu', 'ferme']);
    }
}
