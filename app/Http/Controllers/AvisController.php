<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Stocker un nouvel avis
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

        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:10',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        Avis::create([
            'manga_id' => $manga->id,
            'user_id' => Auth::id(),
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
            'modere' => false, // Par défaut non modéré
        ]);

        // Mettre à jour la note moyenne du manga
        $manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Votre avis a été ajouté avec succès !');
    }

    /**
     * Modifier un avis existant
     */
    public function update(Request $request, Avis $avis)
    {
        // Vérifier que l'utilisateur est propriétaire de l'avis
        if ($avis->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:10',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $avis->update([
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
            'modere' => false, // Repasser en non modéré après modification
        ]);

        // Mettre à jour la note moyenne
        $avis->manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Votre avis a été modifié avec succès !');
    }

    /**
     * Supprimer un avis
     */
    public function destroy(Avis $avis)
    {
        // Vérifier que l'utilisateur est propriétaire ou admin
        if ($avis->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $manga = $avis->manga;
        $avis->delete();

        // Mettre à jour la note moyenne
        $manga->updateNoteMoyenne();

        return redirect()->back()->with('success', 'Avis supprimé avec succès !');
    }

    /**
     * Modérer un avis (admin uniquement)
     */
    public function moderate(Avis $avis)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $avis->update(['modere' => true]);

        return redirect()->back()->with('success', 'Avis modéré avec succès !');
    }
}
