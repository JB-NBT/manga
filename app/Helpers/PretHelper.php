<?php

namespace App\Helpers;

use App\Enums\StatutPret;

class PretHelper
{
    /**
     * Retourne le badge HTML pour un statut de prêt.
     */
    public static function badgePret(string|StatutPret $statut): string
    {
        if (is_string($statut)) {
            if ($statut === 'non_partage') {
                return '<span class="badge" style="background-color: #d1d5db; color: #1f2937;">Non partagé</span>';
            }
            $statut = StatutPret::tryFrom($statut) ?? StatutPret::DISPONIBLE;
        }

        $label = $statut->label();
        $couleur = $statut->couleur();

        return "<span class=\"badge\" style=\"background-color: {$couleur}; color: white;\">{$label}</span>";
    }

    /**
     * Retourne la couleur CSS pour un statut de prêt.
     */
    public static function couleurStatut(string|StatutPret $statut): string
    {
        if (is_string($statut)) {
            if ($statut === 'non_partage') {
                return '#d1d5db';
            }
            $statut = StatutPret::tryFrom($statut) ?? StatutPret::DISPONIBLE;
        }

        return $statut->couleur();
    }

    /**
     * Retourne le label d'un statut de prêt.
     */
    public static function labelStatut(string|StatutPret $statut): string
    {
        if (is_string($statut)) {
            if ($statut === 'non_partage') {
                return 'Non partagé';
            }
            $statut = StatutPret::tryFrom($statut) ?? StatutPret::DISPONIBLE;
        }

        return $statut->label();
    }
}
