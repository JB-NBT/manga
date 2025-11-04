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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Modèle User - Représente un utilisateur de l'application
 *
 * Ce modèle gère les utilisateurs avec leurs rôles (admin, moderator, user)
 * et leurs relations avec les mangas, tickets et avis.
 *
 * @package    App\Models
 * @author     MangaLibrary Team
 * @version    1.0.0
 *
 * @property int         $id                 Identifiant unique
 * @property string      $name               Nom de l'utilisateur
 * @property string      $email              Adresse email (unique)
 * @property string      $password           Mot de passe hashé (bcrypt)
 * @property string|null $remember_token     Token de session persistante
 * @property \DateTime   $email_verified_at  Date de vérification email
 * @property \DateTime   $created_at         Date de création
 * @property \DateTime   $updated_at         Date de modification
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Manga[] $mangas
 * @property-read \Illuminate\Database\Eloquent\Collection|Ticket[] $tickets
 * @property-read \Illuminate\Database\Eloquent\Collection|Avis[] $avis
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Attributs masqués lors de la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Définit les casts des attributs.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Récupère tous les mangas de l'utilisateur.
     *
     * @return HasMany<Manga>
     */
    public function mangas(): HasMany
    {
        return $this->hasMany(Manga::class);
    }

    /**
     * Récupère tous les tickets créés par l'utilisateur.
     *
     * @return HasMany<Ticket>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Récupère tous les avis laissés par l'utilisateur.
     *
     * @return HasMany<Avis>
     */
    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class);
    }
}
