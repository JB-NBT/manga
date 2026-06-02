<?php

namespace App\Policies;

use App\Models\Pret;
use App\Models\Tome;
use App\Models\User;

class PretPolicy
{
    /**
     * Détermine si l'utilisateur peut emprunter un tome.
     */
    public function emprunter(User $user, Tome $tome): bool
    {
        // Ne peut pas emprunter son propre tome
        return $tome->manga->user_id !== $user->id && $tome->partage;
    }

    /**
     * Détermine si l'utilisateur peut accepter une demande de prêt.
     */
    public function accepter(User $user, Pret $pret): bool
    {
        // Seul le prêteur peut accepter
        return $user->id === $pret->preteur_id && $pret->statut === 'demande';
    }

    /**
     * Détermine si l'utilisateur peut refuser une demande de prêt.
     */
    public function refuser(User $user, Pret $pret): bool
    {
        // Seul le prêteur peut refuser
        return $user->id === $pret->preteur_id && $pret->statut === 'demande';
    }

    /**
     * Détermine si l'utilisateur peut marquer un prêt comme restitué.
     */
    public function restituer(User $user, Pret $pret): bool
    {
        // Le prêteur peut marquer comme restitué
        return $user->id === $pret->preteur_id && $pret->statut === 'en_cours';
    }

    /**
     * Détermine si l'utilisateur peut annuler une demande.
     */
    public function annulerDemande(User $user, Pret $pret): bool
    {
        // L'emprunteur peut annuler sa demande en attente
        return $user->id === $pret->emprunteur_id && $pret->statut === 'demande';
    }
}
