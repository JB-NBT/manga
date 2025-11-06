<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,bmp|max:5120', // 5MB max
        ]);

        // Gestion de l'upload d'image
        if ($request->hasFile('image')) {
            $validated['image_couverture'] = $request->file('image')->store('mangas', 'public');
        }

        $validated['user_id'] = Auth::id();
        $validated['est_public'] = false; // TOUJOURS privé par défaut

        Manga::create($validated);

        return redirect()->route('mangas.my-collection')
            ->with('success', 'Manga ajouté à votre collection !');
    }

    /**
     * Afficher un manga
     */
    public function show(Manga $manga)
    {
        // Vérification des droits d'accès
        if (!$manga->est_public) {
            // Utilisateur non connecté → interdit
            if (!Auth::check()) {
                abort(403, 'Accès non autorisé.');
            }

            // Utilisateur connecté mais non propriétaire → interdit
            if (Auth::id() !== $manga->user_id && !Auth::user()->hasRole('admin')) {
                abort(403, 'Ce manga est privé.');
            }
        }

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

        // Validation de base sans l'image
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'required|string|max:255',
            'description' => 'nullable|string',
            'nombre_tomes' => 'required|integer|min:1',
            'statut' => 'required|in:en_cours,termine,abandonne',
            'note' => 'nullable|integer|min:1|max:10',
        ]);

        // Validation de l'image uniquement si un fichier est uploadé
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            ]);

            // Supprimer l'ancienne image si elle existe
            if ($manga->image_couverture) {
                Storage::disk('public')->delete($manga->image_couverture);
            }
            
            // Stocker la nouvelle image
            $validated['image_couverture'] = $request->file('image')->store('mangas', 'public');
        }

        $manga->update($validated);

        return redirect()->route('mangas.show', $manga)
            ->with('success', 'Manga mis à jour !');
    }

    /**
     * Supprimer un manga
     */
    public function destroy(Manga $manga)
    {
        $this->authorize('delete', $manga);
        
        // Supprimer l'image si elle existe
        if ($manga->image_couverture) {
            Storage::disk('public')->delete($manga->image_couverture);
        }
        
        $manga->delete();

        return redirect()->route('mangas.my-collection')
            ->with('success', 'Manga supprimé !');
    }
}
