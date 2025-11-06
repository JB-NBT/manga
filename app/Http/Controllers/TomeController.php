<?php

/**
 * MangaLibrary - Application de gestion de collection de mangas
 *
 * @package    MangaLibrary
 * @author     MangaLibrary Team
 * @copyright  2026 MangaLibrary
 * @license    MIT
 * @version    1.0.0
 */

namespace App\Http\Controllers;

use App\Models\Tome;
use App\Models\Manga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Contrôleur TomeController - Gestion des tomes
 *
 * Ce contrôleur gère les opérations CRUD sur les tomes
 * associés à un manga (génération, toggle possession, etc.).
 *
 * @package    App\Http\Controllers
 * @author     MangaLibrary Team
 * @version    1.0.0
 */
class TomeController extends Controller
{
    /**
     * Constructeur - Applique le middleware d'authentification.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche la liste des tomes d'un manga.
     *
     * @param  Manga $manga Le manga dont on affiche les tomes
     * @return View
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function index(Manga $manga): View
    {
        // Vérification des droits d'accès
        if (
            !$manga->est_public &&
            $manga->user_id !== Auth::id() &&
            !Auth::user()->hasRole('admin')
        ) {
            abort(403, 'Accès non autorisé.');
        }

        $tomes = $manga->tomes()->orderBy('numero')->get();

        return view('tomes.index', compact('manga', 'tomes'));
    }

    /**
     * Génère automatiquement les tomes d'un manga.
     *
     * Supprime les tomes existants et crée des tomes numérotés
     * de 1 jusqu'au nombre total de tomes du manga.
     *
     * @param  Manga $manga Le manga pour lequel générer les tomes
     * @return RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function generateTomes(Manga $manga): RedirectResponse
    {
        // Vérification des droits
        if (
            $manga->user_id !== Auth::id() &&
            !Auth::user()->hasRole('admin')
        ) {
            abort(403, 'Action non autorisée.');
        }

        // Suppression des tomes existants
        $manga->tomes()->delete();

        // Création des nouveaux tomes
        for ($i = 1; $i <= $manga->nombre_tomes; $i++) {
            Tome::create([
                'manga_id' => $manga->id,
                'numero' => $i,
                'possede' => false,
            ]);
        }

        return redirect()->route('tomes.index', $manga)
            ->with('success', 'Tomes générés avec succès !');
    }

    /**
     * Inverse l'état de possession d'un tome.
     *
     * Si le tome passe à "possédé", la date d'achat est
     * automatiquement définie à la date du jour.
     *
     * @param  Tome $tome Le tome à modifier
     * @return RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function togglePossede(Tome $tome): RedirectResponse
    {
        // Vérification des droits
        if (
            $tome->manga->user_id !== Auth::id() &&
            !Auth::user()->hasRole('admin')
        ) {
            abort(403, 'Action non autorisée.');
        }

        $tome->update([
            'possede' => !$tome->possede,
            'date_achat' => !$tome->possede ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Statut du tome mis à jour !');
    }

    /**
     * Met à jour les informations d'un tome.
     *
     * @param  Request $request Données de la requête
     * @param  Tome    $tome    Le tome à mettre à jour
     * @return RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function update(Request $request, Tome $tome): RedirectResponse
    {
        // Vérification des droits
        if (
            $tome->manga->user_id !== Auth::id() &&
            !Auth::user()->hasRole('admin')
        ) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'possede' => 'required|boolean',
            'date_achat' => 'nullable|date',
        ]);

        $tome->update($validated);

        return redirect()->back()->with('success', 'Tome mis à jour avec succès !');
    }
}
