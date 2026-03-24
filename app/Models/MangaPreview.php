<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MangaPreview extends Model
{
    use HasFactory;

    protected $fillable = [
        'manga_id',
        'ordre',
        'image_path',
    ];

    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }
}
