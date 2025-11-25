<?php

namespace App\Policies;

use App\Models\Manga;
use App\Models\User;

class MangaPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Manga $manga): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create manga');
    }

    /**
     * Modérateur peut modifier n'importe quel manga
     * Admin peut modifier n'importe quel manga
     * User peut modifier uniquement ses propres mangas
     */
    public function update(User $user, Manga $manga): bool
    {
        // Modérateur peut tout modifier
        if ($user->hasPermissionTo('edit any manga')) {
            return true;
        }
        
        // Propriétaire peut modifier son manga
        return $user->id === $manga->user_id && $user->hasPermissionTo('edit own manga');
    }

    /**
     * SEUL l'admin peut supprimer n'importe quel manga
     * User peut supprimer ses propres mangas
     */
    public function delete(User $user, Manga $manga): bool
    {
        // Admin peut tout supprimer
        if ($user->hasPermissionTo('delete any manga')) {
            return true;
        }
        
        // Propriétaire peut supprimer son manga
        return $user->id === $manga->user_id && $user->hasPermissionTo('delete own manga');
    }

    /**
     * Modérateur et Admin peuvent republier un manga expiré
     */
    public function republish(User $user, Manga $manga): bool
    {
        return $user->hasPermissionTo('republish expired manga');
    }
}
