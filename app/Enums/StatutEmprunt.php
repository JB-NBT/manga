<?php

namespace App\Enums;

enum StatutEmprunt: string
{
    case DEMANDE = 'demande';
    case ACCEPTE = 'accepte';
    case EN_COURS = 'en_cours';
    case RESTITUE = 'restitue';
    case REFUSE = 'refuse';

    /**
     * Retourne le label en français du statut.
     */
    public function label(): string
    {
        return match($this) {
            self::DEMANDE => 'Demande en attente',
            self::ACCEPTE => 'Accepté',
            self::EN_COURS => 'En cours',
            self::RESTITUE => 'Restitué',
            self::REFUSE => 'Refusé',
        };
    }

    /**
     * Retourne la couleur associée au statut.
     */
    public function couleur(): string
    {
        return match($this) {
            self::DEMANDE => '#f59e0b',     // Orange
            self::ACCEPTE => '#3b82f6',     // Bleu
            self::EN_COURS => '#ef4444',    // Rouge
            self::RESTITUE => '#10b981',    // Vert
            self::REFUSE => '#6b7280',      // Gris
        };
    }

    /**
     * Retourne la classe CSS Bootstrap associée.
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::DEMANDE => 'badge-warning',
            self::ACCEPTE => 'badge-info',
            self::EN_COURS => 'badge-danger',
            self::RESTITUE => 'badge-success',
            self::REFUSE => 'badge-secondary',
        };
    }
}
