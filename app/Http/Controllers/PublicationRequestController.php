<?php

namespace App\Http\Controllers;

use App\Models\PublicationRequest;
use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class PublicationRequestController
 *
 * Gère les demandes de publication des mangas.
 *
 * @package App\Http\Controllers
 */
class PublicationRequestController extends Controller
{
    /**
     * Constructeur - exige que l'utilisateur soit connecté.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Crée une demande de publication pour un manga privé.
     *
     * @param Request $request
     * @param Manga   $manga
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Manga $manga)
    {
        if ($manga->user_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez pas demander la publication de ce manga.');
        }

        if ($manga->est_public) {
            return redirect()->back()->with('error', 'Ce manga est déjà public.');
        }

        $existingRequest = PublicationRequest::where('manga_id', $manga->id)
            ->where('statut', 'en_attente')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'Une demande de publication est déjà en cours pour ce manga.');
        }

        $validated = $request->validate([
            'message_utilisateur' => 'nullable|string|max:500',
        ]);

        PublicationRequest::create([
            'manga_id' => $manga->id,
            'user_id' => Auth::id(),
            'statut' => 'en_attente',
            'message_utilisateur' => $validated['message_utilisateur'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Votre demande de publication a été envoyée !');
    }

    /**
     * Liste toutes les demandes en attente (Admin uniquement).
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $requests = PublicationRequest::with(['manga', 'user'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.publication-requests.index', compact('requests'));
    }

    /**
     * Approuve une demande de publication et rend le manga public.
     *
     * @param Request              $request
     * @param PublicationRequest   $publicationRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, PublicationRequest $publicationRequest)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'message_admin' => 'nullable|string|max:500',
        ]);

        $publicationRequest->update([
            'statut' => 'approuve',
            'message_admin' => $validated['message_admin'] ?? null,
            'date_traitement' => now(),
        ]);

        $publicationRequest->manga->update(['est_public' => true]);

        return redirect()->back()->with('success', 'Demande approuvée ! Le manga est maintenant public.');
    }

    /**
     * Refuse une demande de publication.
     *
     * @param Request              $request
     * @param PublicationRequest   $publicationRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, PublicationRequest $publicationRequest)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'message_admin' => 'required|string|max:500',
        ]);

        $publicationRequest->update([
            'statut' => 'refuse',
            'message_admin' => $validated['message_admin'],
            'date_traitement' => now(),
        ]);

        return redirect()->back()->with('success', 'Demande refusée.');
    }

    /**
     * Affiche les demandes de publication de l'utilisateur connecté.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function myRequests()
    {
        $requests = PublicationRequest::with('manga')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mangas.my-requests', compact('requests'));
    }
}

