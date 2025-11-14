<?php

namespace App\Http\Controllers;

use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Class MangaController
 *
 * Gère la création, l'affichage, la modification et la suppression de mangas.
 *
 * @package App\Http\Controllers
 */
class MangaController extends Controller
{
    /**
     * Constructeur - protège les routes sauf index() et show().
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Page d'accueil : liste uniquement les mangas publics.
     *
     * @return \Illuminate\Contracts\View\View
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
     * Affiche la collection de l'utilisateur.
     * Admin : tous les mangas.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function myCollection()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $mangas = Manga::with('user')->orderBy('created_at', 'desc')->paginate(12);
            $title = "Tous les mangas (Admin)";
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
     * Affiche le formulaire de création.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $this->authorize('create', Manga::class);
        return view('mangas.create');
    }

    /**
     * Enregistre un nouveau manga privé.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
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
            'url_lecture_index' => 'nullable|url|max:500',
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
     * Affiche un manga, si l'utilisateur est autorisé.
     *
     * @param Manga $manga
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Manga $manga)
    {
        if (!$manga->est_public) {
            if (!Auth::check()) {
                abort(403, 'Accès non autorisé.');
            }
            if (Auth::id() !== $manga->user_id && !Auth::user()->hasRole('admin')) {
                abort(403, 'Ce manga est privé.');
            }
        }

        $manga->load('user', 'avis.user');

        return view('mangas.show', compact('manga'));
    }

    /**
     * Formulaire d'édition.
     *
     * @param Manga $manga
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Manga $manga)
    {
        $this->authorize('update', $manga);

        return view('mangas.edit', compact('manga'));
    }

    /**
     * Met à jour un manga.
     *
     * @param Request $request
     * @param Manga $manga
     * @return \Illuminate\Http\RedirectResponse
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
            'url_lecture_index' => 'nullable|url|max:500',
        ]);

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            ]);

            if ($manga->image_couverture) {
                Storage::disk('public')->delete($manga->image_couverture);
            }

            $validated['image_couverture'] = $request->file('image')->store('mangas', 'public');
        }

        $manga->update($validated);

        return redirect()->route('mangas.show', $manga)
            ->with('success', 'Manga mis à jour !');
    }

    /**
     * Supprime un manga.
     *
     * @param Manga $manga
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Manga $manga)
    {
        $this->authorize('delete', $manga);

        if ($manga->image_couverture) {
            Storage::disk('public')->delete($manga->image_couverture);
        }

        $manga->delete();

        return redirect()->route('mangas.my-collection')
            ->with('success', 'Manga supprimé !');
    }
}

