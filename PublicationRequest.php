<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'manga_id',
        'user_id',
        'statut',
        'message_utilisateur',
        'message_admin',
        'date_traitement',
    ];

    protected $casts = [
        'date_demande' => 'datetime',
        'date_traitement' => 'datetime',
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
