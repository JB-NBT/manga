<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    use HasFactory;

    protected $table = 'avis';

    protected $fillable = [
        'manga_id',
        'user_id',
        'note',
        'commentaire',
        'modere',
    ];

    protected $casts = [
        'modere' => 'boolean',
    ];

    // Relations
    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
