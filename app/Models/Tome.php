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
 * Modèle Tome - Représente un tome d'un manga
 *
 * Ce modèle gère les tomes individuels d'une collection de manga,
 * incluant le suivi de possession et la date d'achat.
 *
 * @package    App\Models
 * @author     MangaLibrary Team
 * @version    1.0.0
 *
 * @property int              $id          Identifiant unique
 * @property int              $manga_id    ID du manga parent
 * @property int              $numero      Numéro du tome
 * @property bool             $possede     Indique si le tome est possédé
 * @property \DateTime|null   $date_achat  Date d'achat du tome
 * @property \DateTime        $created_at  Date de création
 * @property \DateTime        $updated_at  Date de modification
 *
 * @property-read Manga $manga
 */
class Tome extends Model
{
    use HasFactory;

    /**
     * Attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'manga_id',
        'numero',
        'possede',
        'date_achat',
    ];

    /**
     * Définit les casts des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'possede' => 'boolean',
        'date_achat' => 'date',
    ];

    /**
     * Récupère le manga parent.
     *
     * @return BelongsTo<Manga, Tome>
     */
    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }
}
