<?php

namespace App\Http\Controllers;

use App\Models\Tome;
use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des tomes d'un manga
     */
    public function index(Manga $manga)
    {
        // Vérifier l'accès
        if (!$manga->est_public && $manga->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé.');
        }

        $tomes = $manga->tomes()->orderBy('numero')->get();
        
        return view('tomes.index', compact('manga', 'tomes'));
    }

    /**
     * Créer automatiquement les tomes pour un manga
     */
    public function generateTomes(Manga $manga)
    {
        // Vérifier que l'utilisateur est propriétaire
        if ($manga->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }

        // Supprimer les tomes existants
        $manga->tomes()->delete();

        // Créer les nouveaux tomes
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
     * Marquer un tome comme possédé/non possédé
     */
    public function togglePossede(Tome $tome)
    {
        // Vérifier que l'utilisateur est propriétaire du manga
        if ($tome->manga->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $tome->update([
            'possede' => !$tome->possede,
            'date_achat' => !$tome->possede ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Statut du tome mis à jour !');
    }

    /**
     * Mettre à jour un tome
     */
    public function update(Request $request, Tome $tome)
    {
        // Vérifier que l'utilisateur est propriétaire du manga
        if ($tome->manga->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'possede' => 'required|boolean',
            'date_achat' => 'nullable|date',
            'url_lecture' => 'nullable|url|max:500',
        ]);

        $tome->update($validated);

        return redirect()->back()->with('success', 'Tome mis à jour avec succès !');
    }
}
