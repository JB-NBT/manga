<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'date_derniere_republication',
    ];

    protected $casts = [
        'est_public' => 'boolean',
        'note_moyenne' => 'decimal:1',
        'date_derniere_republication' => 'datetime',
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

    /**
     * Vérifie si un manga public a expiré (1 an sans republication)
     * 
     * @return bool
     */
    public function isExpired(): bool
    {
        if (!$this->est_public) {
            return false;
        }

        // Si date_derniere_republication existe, on l'utilise
        if ($this->date_derniere_republication) {
            return $this->date_derniere_republication->addYear()->isPast();
        }

        // Sinon on utilise la date de création
        return $this->created_at->addYear()->isPast();
    }

    /**
     * Retirer un manga de la publication (protection copyright)
     */
    public function unpublish(): void
    {
        $this->update([
            'est_public' => false,
        ]);
    }

    /**
     * Republier un manga (modérateur/admin uniquement)
     */
    public function republish(): void
    {
        $this->update([
            'est_public' => true,
            'date_derniere_republication' => now(),
        ]);
    }

    /**
     * Scope pour récupérer les mangas expirés
     */
    public function scopeExpired($query)
    {
        return $query->where('est_public', true)
            ->where(function ($q) {
                $q->whereNull('date_derniere_republication')
                  ->where('created_at', '<', Carbon::now()->subYear())
                  ->orWhere('date_derniere_republication', '<', Carbon::now()->subYear());
            });
    }

    /**
     * Scope pour récupérer les mangas bientôt expirés (30 jours)
     */
    public function scopeExpiringSoon($query)
    {
        $dateLimit = Carbon::now()->subDays(335); // 365 - 30 = 335 jours

        return $query->where('est_public', true)
            ->where(function ($q) use ($dateLimit) {
                $q->whereNull('date_derniere_republication')
                  ->where('created_at', '<', $dateLimit)
                  ->orWhere('date_derniere_republication', '<', $dateLimit);
            });
    }
}
