<?php

namespace App\Http\Controllers;

use App\Models\PublicationRequest;
use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Gère les demandes de publication des mangas
 * Validation = Modérateur uniquement
 */
class PublicationRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Crée une demande de publication pour un manga privé
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
     * Liste toutes les demandes en attente (Modérateur uniquement)
     */
    public function index()
    {
        if (!Auth::user()->hasPermissionTo('approve publications')) {
            abort(403, 'Accès réservé aux modérateurs.');
        }

        $requests = PublicationRequest::with(['manga', 'user'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.publication-requests.index', compact('requests'));
    }

    /**
     * Approuve une demande de publication et rend le manga public
     * (Modérateur uniquement)
     */
    public function approve(Request $request, PublicationRequest $publicationRequest)
    {
        if (!Auth::user()->hasPermissionTo('approve publications')) {
            abort(403, 'Action réservée aux modérateurs.');
        }

        $validated = $request->validate([
            'message_admin' => 'nullable|string|max:500',
        ]);

        $publicationRequest->update([
            'statut' => 'approuve',
            'message_admin' => $validated['message_admin'] ?? null,
            'date_traitement' => now(),
        ]);

        $publicationRequest->manga->update([
            'est_public' => true,
            'date_derniere_republication' => now(), // Initialise la date de publication
        ]);

        return redirect()->back()->with('success', 'Demande approuvée ! Le manga est maintenant public.');
    }

    /**
     * Refuse une demande de publication
     * (Modérateur uniquement)
     */
    public function reject(Request $request, PublicationRequest $publicationRequest)
    {
        if (!Auth::user()->hasPermissionTo('approve publications')) {
            abort(403, 'Action réservée aux modérateurs.');
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
     * Affiche les demandes de publication de l'utilisateur connecté
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
