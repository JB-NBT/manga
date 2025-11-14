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
 * Modèle Avis - Représente un avis/commentaire sur un manga
 *
 * Ce modèle gère les avis des utilisateurs sur les mangas publics,
 * incluant une note (1-10) et un commentaire optionnel.
 * Un utilisateur ne peut laisser qu'un seul avis par manga.
 *
 * @package    App\Models
 * @author     MangaLibrary Team
 * @version    1.0.0
 *
 * @property int         $id           Identifiant unique
 * @property int         $manga_id     ID du manga concerné
 * @property int         $user_id      ID de l'auteur de l'avis
 * @property int         $note         Note de 1 à 10
 * @property string|null $commentaire  Commentaire optionnel (max 1000 caractères)
 * @property bool        $modere       Indique si l'avis a été modéré
 * @property \DateTime   $created_at   Date de création
 * @property \DateTime   $updated_at   Date de modification
 *
 * @property-read Manga $manga
 * @property-read User  $user
 */
class Avis extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée.
     *
     * @var string
     */
    protected $table = 'avis';

    /**
     * Attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'manga_id',
        'user_id',
        'note',
        'commentaire',
        'modere',
    ];

    /**
     * Définit les casts des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'modere' => 'boolean',
    ];

    /**
     * Récupère le manga associé à l'avis.
     *
     * @return BelongsTo<Manga, Avis>
     */
    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }

    /**
     * Récupère l'auteur de l'avis.
     *
     * @return BelongsTo<User, Avis>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
