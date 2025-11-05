<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MangaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Page d'accueil - Affiche uniquement les mangas publics
     */
    public function index()
    {
        $mangas = Manga::where('est_public', true)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('mangas.index', compact('mangas'));
    }

    /**
     * Ma Collection - Affiche les mangas privés de l'utilisateur
     * ADMIN : voit TOUS les mangas (publics et privés)
     */
    public function myCollection()
    {
        $user = Auth::user();

        // Si admin : voir TOUS les mangas
        if ($user->hasRole('admin')) {
            $mangas = Manga::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(12);
            
            $title = "Tous les mangas (Admin)";
        } 
        // Si user : voir uniquement SES mangas privés
        else {
            $mangas = Manga::where('user_id', $user->id)
                ->where('est_public', false)
                ->orderBy('created_at', 'desc')
                ->paginate(12);
            
            $title = "Ma Collection";
        }

        return view('mangas.my-collection', compact('mangas', 'title'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $this->authorize('create', Manga::class);
        return view('mangas.create');
    }

    /**
     * Enregistrer un nouveau manga (toujours privé par défaut)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Manga::class);

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'description' => 'nullable|string',
            'nombre_tomes' => 'required|integer|min:1',
            'statut' => 'required|in:en_cours,termine,abandonne',
            'note' => 'nullable|integer|min:1|max:10',
        ]);

        $manga = Manga::create([
            'user_id' => Auth::id(),
            'titre' => $validated['titre'],
            'auteur' => $validated['auteur'],
            'description' => $validated['description'],
            'nombre_tomes' => $validated['nombre_tomes'],
            'statut' => $validated['statut'],
            'note' => $validated['note'],
            'est_public' => false, // TOUJOURS privé par défaut
        ]);

        return redirect()->route('mangas.my-collection')
            ->with('success', 'Manga ajouté à votre collection !');
    }

    /**
     * Afficher un manga
     */
    public function show(Manga $manga)
    {
        $manga->load('user', 'avis.user');
        return view('mangas.show', compact('manga'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Manga $manga)
    {
        $this->authorize('update', $manga);
        return view('mangas.edit', compact('manga'));
    }

    /**
     * Mettre à jour un manga
     */
    public function update(Request $request, Manga $manga)
    {
        $this->authorize('update', $manga);

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'description' => 'nullable|string',
            'nombre_tomes' => 'required|integer|min:1',
            'statut' => 'required|in:en_cours,termine,abandonne',
            'note' => 'nullable|integer|min:1|max:10',
        ]);

        $manga->update($validated);

        return redirect()->route('mangas.my-collection')
            ->with('success', 'Manga mis à jour !');
    }

    /**
     * Supprimer un manga
     */
    public function destroy(Manga $manga)
    {
        $this->authorize('delete', $manga);
        $manga->delete();

        return redirect()->route('mangas.my-collection')
            ->with('success', 'Manga supprimé !');
    }
}
