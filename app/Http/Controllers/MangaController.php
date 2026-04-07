<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use App\Models\MangaPreview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Gère l'affichage et la gestion des mangas (collection privée et bibliothèque publique).
 */
class MangaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Affiche la bibliothèque publique avec recherche optionnelle par titre ou auteur.
     */
    public function index(Request $request)
    {
        $query = Manga::where('est_public', true)->with(['user', 'previews']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'LIKE', "%{$search}%")
                  ->orWhere('auteur', 'LIKE', "%{$search}%");
            });
        }

        $mangas = $query->orderBy('created_at', 'desc')->paginate(9);

        return view('mangas.index', compact('mangas'));
    }

    /**
     * Affiche la collection privée de l'utilisateur connecté.
     * Les admins et modérateurs voient tous les mangas.
     */
    public function myCollection()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $mangas = Manga::with('user')->orderBy('created_at', 'desc')->paginate(12);
            $title = "Tous les mangas (Admin)";
        } elseif ($user->hasRole('moderator')) {
            $mangas = Manga::with('user')->orderBy('created_at', 'desc')->paginate(12);
            $title = "Tous les mangas (Modérateur)";
        } else {
            $mangas = Manga::where('user_id', $user->id)
                ->where('est_public', false)
                ->orderBy('created_at', 'desc')
                ->paginate(12);
            $title = "Ma Collection";
        }

        return view('mangas.my-collection', compact('mangas', 'title'));
    }

    /**
     * Affiche le formulaire de création d'un manga.
     */
    public function create()
    {
        $this->authorize('create', Manga::class);
        return view('mangas.create');
    }

    /**
     * Enregistre un nouveau manga dans la collection privée de l'utilisateur.
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
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,bmp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_couverture'] = $request->file('image')->store('mangas', 'public');
        }

        $validated['user_id'] = Auth::id();
        $validated['est_public'] = false;

        Manga::create($validated);

        return redirect()->route('mangas.my-collection')
            ->with('success', 'Manga ajouté à votre collection !');
    }

    /**
     * Affiche le détail d'un manga. Les mangas privés ne sont accessibles qu'à leur propriétaire et aux modérateurs.
     */
    public function show(Manga $manga)
    {
        if (!$manga->est_public) {
            if (!Auth::check()) {
                abort(403, 'Accès non autorisé.');
            }
            if (Auth::id() !== $manga->user_id && !Auth::user()->hasAnyRole(['admin', 'moderator'])) {
                abort(403, 'Ce manga est privé.');
            }
        }

        $manga->load('user', 'avis.user', 'previews');

        return view('mangas.show', compact('manga'));
    }

    /**
     * Affiche le formulaire d'édition d'un manga.
     */
    public function edit(Manga $manga)
    {
        $this->authorize('update', $manga);

        return view('mangas.edit', compact('manga'));
    }

    /**
     * Met à jour les informations d'un manga.
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

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            ]);

            if ($manga->image_couverture && Storage::disk('public')->exists($manga->image_couverture)) {
                Storage::disk('public')->delete($manga->image_couverture);
            }

            $validated['image_couverture'] = $request->file('image')->store('mangas', 'public');
        }

        $manga->update($validated);

        return redirect()->route('mangas.show', $manga)
            ->with('success', 'Manga mis à jour !');
    }

    /**
     * Supprime un manga et son image de couverture associée.
     */
    public function destroy(Manga $manga)
    {
        $this->authorize('delete', $manga);

        if ($manga->image_couverture && Storage::disk('public')->exists($manga->image_couverture)) {
            Storage::disk('public')->delete($manga->image_couverture);
        }

        $manga->delete();

        return redirect()->route('mangas.my-collection')
            ->with('success', 'Manga supprimé !');
    }

    /**
     * Upload d'une image de preview (admin/modérateur uniquement)
     */
    public function uploadPreview(Request $request, Manga $manga)
    {
        $this->authorize('uploadPreview', $manga);

        $validated = $request->validate([
            'ordre' => 'required|integer|in:1,2',
            'image' => 'required|file|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $existingPreview = $manga->previews()->where('ordre', $validated['ordre'])->first();
        if ($existingPreview) {
            if (Storage::disk('public')->exists($existingPreview->image_path)) {
                Storage::disk('public')->delete($existingPreview->image_path);
            }
            $existingPreview->delete();
        }

        $path = $request->file('image')->store('previews', 'public');

        MangaPreview::create([
            'manga_id' => $manga->id,
            'ordre' => $validated['ordre'],
            'image_path' => $path,
        ]);

        return redirect()->back()->with('success', 'Image de preview page ' . $validated['ordre'] . ' uploadée !');
    }

    /**
     * Suppression d'une image de preview (admin/modérateur uniquement)
     */
    public function deletePreview(Manga $manga, MangaPreview $preview)
    {
        $this->authorize('uploadPreview', $manga);

        if (Storage::disk('public')->exists($preview->image_path)) {
            Storage::disk('public')->delete($preview->image_path);
        }
        $preview->delete();

        return redirect()->back()->with('success', 'Image de preview supprimée.');
    }

}
