<?php

namespace App\Http\Controllers;

use App\Models\Tome;
use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class TomeController
 *
 * Gère les tomes associés à un manga.
 *
 * @package App\Http\Controllers
 */
class TomeController extends Controller
{
    /**
     * Constructeur - nécessite une authentification.
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
     * @param Manga $manga
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Manga $manga)
    {
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
     * Génère automatiquement les tomes d'un manga (du tome 1 au tome total).
     *
     * @param Manga $manga
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateTomes(Manga $manga)
    {
        if (
            $manga->user_id !== Auth::id() &&
            !Auth::user()->hasRole('admin')
        ) {
            abort(403, 'Action non autorisée.');
        }

        $manga->tomes()->delete();

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
     * Inverse l’état "possédé" d’un tome.
     *
     * @param Tome $tome
     * @return \Illuminate\Http\RedirectResponse
     */
    public function togglePossede(Tome $tome)
    {
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
     * Met à jour les informations d’un tome.
     *
     * @param Request $request
     * @param Tome    $tome
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tome $tome)
    {
        if (
            $tome->manga->user_id !== Auth::id() &&
            !Auth::user()->hasRole('admin')
        ) {
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

