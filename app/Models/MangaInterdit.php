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
 * Modèle MangaInterdit - Représente un contenu interdit
 *
 * Ce modèle gère la liste noire des contenus interdits (titres, mots-clés)
 * qui ne doivent pas apparaître sur la plateforme.
 *
 * @package    App\Models
 * @author     MangaLibrary Team
 * @version    1.0.0
 *
 * @property int              $id                 Identifiant unique
 * @property string           $titre              Titre ou mot-clé interdit
 * @property string|null      $auteur             Auteur (optionnel)
 * @property string|null      $categorie          Catégorie de contenu interdit
 * @property string           $raison             Raison de l'interdiction
 * @property int              $ajoute_par         ID de l'admin/modérateur
 * @property \DateTime        $date_interdiction  Date d'ajout à la liste
 * @property \DateTime        $created_at         Date de création
 * @property \DateTime        $updated_at         Date de modification
 *
 * @property-read User $ajoutePar
 * @property-read string $categorie_libelle
 */
class MangaInterdit extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée.
     *
     * @var string
     */
    protected $table = 'mangas_interdits';

    /**
     * Catégories prédéfinies de contenus interdits.
     * Ces catégories représentent des TYPES de contenu, pas des titres spécifiques.
     *
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'hentai' => 'Contenu adulte/hentai',
        'gore' => 'Violence extrême/gore',
        'pedophile' => 'Contenu impliquant des mineurs',
        'incitation_haine' => 'Incitation à la haine',
        'terrorisme' => 'Apologie du terrorisme',
        'copyright' => 'Violation de droits d\'auteur',
        'autre' => 'Autre raison',
    ];

    /**
     * Attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titre',
        'auteur',
        'categorie',
        'raison',
        'ajoute_par',
        'date_interdiction',
    ];

    /**
     * Définit les casts des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_interdiction' => 'date',
    ];

    /**
     * Récupère l'utilisateur qui a ajouté l'interdiction.
     *
     * @return BelongsTo<User, MangaInterdit>
     */
    public function ajoutePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ajoute_par');
    }

    /**
     * Accesseur pour obtenir le libellé de la catégorie.
     *
     * @return string
     */
    public function getCategorieLibelleAttribute(): string
    {
        return self::CATEGORIES[$this->categorie] ?? 'Non catégorisé';
    }

    /**
     * Scope pour rechercher par titre.
     *
     * @param  Builder $query
     * @param  string  $titre
     * @return Builder
     */
    public function scopeRechercheTitre(Builder $query, string $titre): Builder
    {
        return $query->where('titre', 'LIKE', '%' . $titre . '%');
    }

    /**
     * Scope pour filtrer par catégorie.
     *
     * @param  Builder $query
     * @param  string  $categorie
     * @return Builder
     */
    public function scopeCategorie(Builder $query, string $categorie): Builder
    {
        return $query->where('categorie', $categorie);
    }

    /**
     * Vérifie si un titre est dans la liste des interdits.
     *
     * @param  string $titre Titre à vérifier
     * @return bool   True si le titre est interdit
     */
    public static function estInterdit(string $titre): bool
    {
        return self::where('titre', 'LIKE', '%' . $titre . '%')->exists();
    }

    /**
     * Vérifie si un titre correspond à un contenu interdit.
     *
     * @param  string     $titre Titre à vérifier
     * @return array|null Détails de l'interdiction ou null
     */
    public static function verifierInterdit(string $titre): ?array
    {
        $interdit = self::where('titre', 'LIKE', '%' . $titre . '%')->first();

        if ($interdit) {
            return [
                'interdit' => true,
                'categorie' => $interdit->categorie,
                'raison' => $interdit->raison,
            ];
        }

        return null;
    }
}
