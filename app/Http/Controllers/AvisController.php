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

use App\Models\Avis;
use App\Models\Manga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur AvisController - Gestion des avis
 *
 * Ce contrôleur gère les opérations CRUD sur les avis
 * des utilisateurs concernant les mangas publics.
 *
 * @package    App\Http\Controllers
 * @author     MangaLibrary Team
 * @version    1.0.0
 */
class AvisController extends Controller
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
     * Enregistre un nouvel avis pour un manga public.
     *
     * Un utilisateur ne peut laisser qu'un seul avis par manga.
     * Seuls les mangas publics peuvent recevoir des avis.
     *
     * @param  Request $request Données de la requête
     * @param  Manga   $manga   Le manga concerné
     * @return RedirectResponse
     */
    public function store(Request $request, Manga $manga): RedirectResponse
    {
        // Vérification : manga public uniquement
        if (!$manga->est_public) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas noter un manga privé.');
        }

        // Vérification : un seul avis par utilisateur
        $existingAvis = Avis::where('manga_id', $manga->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingAvis) {
            return redirect()->back()->with('error', 'Vous avez déjà noté ce manga.');
        }

        // Validation des données
        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:10',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        // Création de l'avis
        Avis::create([
            'manga_id' => $manga->id,
            'user_id' => Auth::id(),
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
            'modere' => false,
        ]);

        // Mise à jour de la note moyenne
        $manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Votre avis a été ajouté avec succès !');
    }

    /**
     * Met à jour un avis existant.
     *
     * L'utilisateur peut modifier son propre avis.
     * Les modérateurs peuvent modifier n'importe quel avis.
     *
     * @param  Request $request Données de la requête
     * @param  Avis    $avis    L'avis à modifier
     * @return RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function update(Request $request, Avis $avis): RedirectResponse
    {
        // Vérification des droits
        if ($avis->user_id !== Auth::id() && !Auth::user()->hasPermissionTo('moderate avis')) {
            abort(403, 'Action non autorisée.');
        }

        // Validation des données
        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:10',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        // Mise à jour de l'avis
        $avis->update([
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
            'modere' => false,
        ]);

        // Mise à jour de la note moyenne
        $avis->manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Votre avis a été modifié avec succès !');
    }

    /**
     * Supprime un avis.
     *
     * L'utilisateur peut supprimer son propre avis.
     * Les modérateurs peuvent supprimer n'importe quel avis.
     *
     * @param  Avis $avis L'avis à supprimer
     * @return RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function destroy(Avis $avis): RedirectResponse
    {
        // Vérification des droits
        if ($avis->user_id !== Auth::id() && !Auth::user()->hasPermissionTo('moderate avis')) {
            abort(403, 'Action non autorisée.');
        }

        $manga = $avis->manga;
        $avis->delete();

        // Mise à jour de la note moyenne
        $manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Avis supprimé avec succès !');
    }

    /**
     * Marque un avis comme modéré.
     *
     * Accessible uniquement aux modérateurs.
     *
     * @param  Avis $avis L'avis à modérer
     * @return RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function moderate(Avis $avis): RedirectResponse
    {
        // Vérification des droits
        if (!Auth::user()->hasPermissionTo('moderate avis')) {
            abort(403, 'Action non autorisée.');
        }

        $avis->update(['modere' => true]);

        return redirect()->back()->with('success', 'Avis modéré avec succès !');
    }
}
