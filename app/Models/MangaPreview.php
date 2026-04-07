<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Représente une image de preview associée à un manga.
 * Chaque manga peut avoir au maximum 2 previews (ordre 1 et 2).
 *
 * @property int $id
 * @property int $manga_id
 * @property int $ordre
 * @property string $image_path
 */
class MangaPreview extends Model
{
    use HasFactory;

    protected $fillable = [
        'manga_id',
        'ordre',
        'image_path',
    ];

    /**
     * Manga auquel appartient cette preview.
     */
    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }
}
