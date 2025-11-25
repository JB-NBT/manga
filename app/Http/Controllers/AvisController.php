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
     */
    public function store(Request $request, Manga $manga)
    {
        if (!$manga->est_public) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas noter un manga privé.');
        }

        $existingAvis = Avis::where('manga_id', $manga->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingAvis) {
            return redirect()->back()->with('error', 'Vous avez déjà noté ce manga.');
        }

        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:10',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        Avis::create([
            'manga_id' => $manga->id,
            'user_id' => Auth::id(),
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
            'modere' => false,
        ]);

        $manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Votre avis a été ajouté avec succès !');
    }

    // ========================================
    // MODIFIER UN AVIS EXISTANT
    // ========================================
    public function update(Request $request, Avis $avis)
    {
        if ($avis->user_id !== Auth::id() && !Auth::user()->hasPermissionTo('moderate avis')) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:10',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $avis->update([
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
            'modere' => false,
        ]);

        $avis->manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Votre avis a été modifié avec succès !');
    }

    // ========================================
    // SUPPRIMER UN AVIS
    // ========================================
    public function destroy(Avis $avis)
    {
        if ($avis->user_id !== Auth::id() && !Auth::user()->hasPermissionTo('moderate avis')) {
            abort(403, 'Action non autorisée.');
        }

        $manga = $avis->manga;
        $avis->delete();
        $manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Avis supprimé avec succès !');
    }

    // ========================================
    // MODÉRER UN AVIS (MODÉRATEUR UNIQUEMENT)
    // ========================================
    /**
     * Marquer un avis comme modéré
     */
    public function moderate(Avis $avis)
    {
        if (!Auth::user()->hasPermissionTo('moderate avis')) {
            abort(403, 'Action non autorisée.');
        }

        $avis->update(['modere' => true]);

        return redirect()->back()->with('success', 'Avis modéré avec succès !');
    }
}
