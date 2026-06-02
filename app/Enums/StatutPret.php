<?php

namespace App\Enums;

enum StatutPret: string
{
    case DISPONIBLE = 'disponible';
    case DEMANDE = 'demande';
    case PRETE = 'prete';
    case RESTITUE = 'restitue';

    /**
     * Retourne le label en français du statut.
     */
    public function label(): string
    {
        return match($this) {
            self::DISPONIBLE => 'Disponible',
            self::DEMANDE => 'Demande en cours',
            self::PRETE => 'Prêté',
            self::RESTITUE => 'Restitué',
        };
    }

    /**
     * Retourne la couleur associée au statut.
     */
    public function couleur(): string
    {
        return match($this) {
            self::DISPONIBLE => '#10b981',  // Vert
            self::DEMANDE => '#f59e0b',     // Orange
            self::PRETE => '#ef4444',       // Rouge
            self::RESTITUE => '#6b7280',    // Gris
        };
    }

    /**
     * Retourne la classe CSS Bootstrap associée.
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::DISPONIBLE => 'badge-success',
            self::DEMANDE => 'badge-warning',
            self::PRETE => 'badge-danger',
            self::RESTITUE => 'badge-secondary',
        };
    }
}
