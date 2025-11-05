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

    public function update(User $user, Manga $manga): bool
    {
        if ($user->hasPermissionTo('manage all mangas')) {
            return true;
        }
        
        return $user->id === $manga->user_id && $user->hasPermissionTo('edit own manga');
    }

    public function delete(User $user, Manga $manga): bool
    {
        if ($user->hasPermissionTo('manage all mangas')) {
            return true;
        }
        
        return $user->id === $manga->user_id && $user->hasPermissionTo('delete own manga');
    }
}
