<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manga;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur de gestion du copyright (Modérateur uniquement)
 */
class CopyrightController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche la page de gestion du copyright
     * (Modérateur uniquement)
     */
    public function index()
    {
        // Vérifier que l'utilisateur est modérateur
        if (!Auth::user()->hasPermissionTo('republish expired manga')) {
            abort(403, 'Accès réservé aux modérateurs.');
        }

        // Récupérer les mangas expirés (retirés de la publication)
        $expiredMangas = Manga::where('est_public', false)
            ->whereNotNull('date_derniere_republication')
            ->orWhere(function ($query) {
                $query->where('est_public', false)
                    ->whereNull('date_derniere_republication')
                    ->whereRaw('created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)');
            })
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Récupérer les mangas bientôt expirés (30 jours avant)
        $expiringSoonMangas = Manga::expiringSoon()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return view('admin.copyright-management', compact('expiredMangas', 'expiringSoonMangas'));
    }
}
