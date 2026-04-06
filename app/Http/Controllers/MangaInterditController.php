<?php

namespace App\Http\Controllers;

use App\Models\MangaInterdit;
use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Gère la liste des mangas interdits
 * Accessible uniquement aux modérateurs et admins
 */
class MangaInterditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Liste tous les mangas interdits
     */
    public function index(Request $request)
    {
        if (!Auth::user()->hasAnyPermission(['moderate avis', 'manage users'])) {
            abort(403, 'Accès réservé aux modérateurs et administrateurs.');
        }

        $query = MangaInterdit::with('ajoutePar');

        // Filtrer par catégorie si demandé
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        $mangasInterdits = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = MangaInterdit::CATEGORIES;

        // Compter les mangas potentiellement en infraction
        $mangasEnInfraction = $this->detecterInfractions();

        return view('admin.mangas-interdits.index', compact('mangasInterdits', 'categories', 'mangasEnInfraction'));
    }

    /**
     * Formulaire d'ajout d'un manga interdit
     */
    public function create()
    {
        if (!Auth::user()->hasAnyPermission(['moderate avis', 'manage users'])) {
            abort(403, 'Accès réservé aux modérateurs et administrateurs.');
        }

        $categories = MangaInterdit::CATEGORIES;

        return view('admin.mangas-interdits.create', compact('categories'));
    }

    /**
     * Enregistre un nouveau manga interdit
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasAnyPermission(['moderate avis', 'manage users'])) {
            abort(403, 'Accès réservé aux modérateurs et administrateurs.');
        }

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'nullable|string|max:255',
            'categorie' => 'required|string|in:' . implode(',', array_keys(MangaInterdit::CATEGORIES)),
            'raison' => 'required|string|max:1000',
        ]);

        MangaInterdit::create([
            'titre' => $validated['titre'],
            'auteur' => $validated['auteur'] ?? null,
            'categorie' => $validated['categorie'],
            'raison' => $validated['raison'],
            'ajoute_par' => Auth::id(),
            'date_interdiction' => now(),
        ]);

        return redirect()->route('admin.mangas-interdits.index')
            ->with('success', 'Manga ajouté à la liste des interdits.');
    }

    /**
     * Supprime un manga de la liste des interdits
     */
    public function destroy(MangaInterdit $mangaInterdit)
    {
        if (!Auth::user()->hasAnyPermission(['moderate avis', 'manage users'])) {
            abort(403, 'Accès réservé aux modérateurs et administrateurs.');
        }

        $mangaInterdit->delete();

        return redirect()->route('admin.mangas-interdits.index')
            ->with('success', 'Manga retiré de la liste des interdits.');
    }

    /**
     * Recherche si un titre est interdit (utilisé lors de la création de manga)
     */
    public function checkTitre(Request $request)
    {
        $titre = $request->input('titre', '');

        $result = MangaInterdit::verifierInterdit($titre);

        if ($result) {
            return response()->json([
                'interdit' => true,
                'categorie' => $result['categorie'],
                'raison' => $result['raison'],
            ]);
        }

        return response()->json([
            'interdit' => false,
        ]);
    }

    /**
     * Détecte les mangas existants qui correspondent à des titres interdits.
     * Utilise une seule requête groupée au lieu d'une requête par interdit.
     */
    private function detecterInfractions(): array
    {
        $infractions = [];
        $interdits = MangaInterdit::all();

        if ($interdits->isEmpty()) {
            return [];
        }

        $query = Manga::with('user');
        foreach ($interdits as $interdit) {
            $query->orWhere('titre', 'LIKE', '%' . $interdit->titre . '%');
        }
        $mangasTrouves = $query->get();

        foreach ($mangasTrouves as $manga) {
            foreach ($interdits as $interdit) {
                if (stripos($manga->titre, $interdit->titre) !== false) {
                    $infractions[] = [
                        'manga' => $manga,
                        'interdit' => $interdit,
                        'proprietaire' => $manga->user,
                    ];
                }
            }
        }

        return $infractions;
    }

    /**
     * Affiche les mangas en infraction
     */
    public function infractions()
    {
        if (!Auth::user()->hasAnyPermission(['moderate avis', 'manage users'])) {
            abort(403, 'Accès réservé aux modérateurs et administrateurs.');
        }

        $infractions = $this->detecterInfractions();

        return view('admin.mangas-interdits.infractions', compact('infractions'));
    }
}
