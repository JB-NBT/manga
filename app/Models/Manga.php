<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manga extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titre',
        'auteur',
        'description',
        'image_couverture',
        'nombre_tomes',
        'statut',
        'note',
        'est_public',
        'note_moyenne',
        'nombre_avis',
    ];

    protected $casts = [
        'est_public' => 'boolean',
        'note_moyenne' => 'decimal:1',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tomes()
    {
        return $this->hasMany(Tome::class);
    }

    public function publicationRequest()
    {
        return $this->hasOne(PublicationRequest::class);
    }

    public function avis()
    {
        return $this->hasMany(Avis::class);
    }

    // Méthode pour calculer la note moyenne
    public function updateNoteMoyenne()
    {
        $moyenne = $this->avis()->avg('note');
        $this->note_moyenne = $moyenne ? round($moyenne, 1) : null;
        $this->nombre_avis = $this->avis()->count();
        $this->save();
    }
}
