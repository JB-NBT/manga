<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvisController extends Controller
{
    // ========================================
    // CONSTRUCTEUR : applique le middleware d'authentification
    // ========================================
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ========================================
    // ENREGISTRER UN NOUVEL AVIS
    // ========================================
    /**
     * Stocker un nouvel avis pour un manga public
     *
     * @param Request $request
     * @param Manga   $manga
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Manga $manga)
    {
        // Vérifier que le manga est public
        if (!$manga->est_public) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas noter un manga privé.');
        }

        // Vérifier si l'utilisateur a déjà noté ce manga
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
            'modere' => false, // Par défaut non modéré
        ]);

        // Mise à jour de la note moyenne du manga
        $manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Votre avis a été ajouté avec succès !');
    }

    // ========================================
    // MODIFIER UN AVIS EXISTANT
    // ========================================
    /**
     * Modifier un avis existant
     *
     * @param Request $request
     * @param Avis    $avis
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Avis $avis)
    {
        // Vérifier que l'utilisateur est propriétaire ou admin
        if ($avis->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
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
            'modere' => false, // Repasser en non modéré après modification
        ]);

        // Mise à jour de la note moyenne du manga
        $avis->manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Votre avis a été modifié avec succès !');
    }

    // ========================================
    // SUPPRIMER UN AVIS
    // ========================================
    /**
     * Supprimer un avis
     *
     * @param Avis $avis
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Avis $avis)
    {
        // Vérifier l'autorisation (propriétaire ou admin)
        if ($avis->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $manga = $avis->manga;

        // Suppression de l'avis
        $avis->delete();

        // Mise à jour de la note moyenne
        $manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Avis supprimé avec succès !');
    }

    // ========================================
    // MODÉRER UN AVIS (ADMIN UNIQUEMENT)
    // ========================================
    /**
     * Marquer un avis comme modéré
     *
     * @param Avis $avis
     * @return \Illuminate\Http\RedirectResponse
     */
    public function moderate(Avis $avis)
    {
        // Autorisation admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }

        // Mise à jour
        $avis->update(['modere' => true]);

        return redirect()->back()->with('success', 'Avis modéré avec succès !');
    }
}

