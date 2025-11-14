<?php

namespace App\Http\Controllers;

use App\Models\PublicationRequest;
use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicationRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Créer une demande de publication
     */
    public function store(Request $request, Manga $manga)
    {
        // Vérifier que l'utilisateur est propriétaire du manga
        if ($manga->user_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez pas demander la publication de ce manga.');
        }

        // Vérifier que le manga n'est pas déjà public
        if ($manga->est_public) {
            return redirect()->back()->with('error', 'Ce manga est déjà public.');
        }

        // Vérifier qu'il n'y a pas déjà une demande en attente
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
     * Afficher toutes les demandes (admin)
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
     * Approuver une demande
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

        // Rendre le manga public
        $publicationRequest->manga->update(['est_public' => true]);

        return redirect()->back()->with('success', 'Demande approuvée ! Le manga est maintenant public.');
    }

    /**
     * Refuser une demande
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
     * Afficher les demandes de l'utilisateur
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
