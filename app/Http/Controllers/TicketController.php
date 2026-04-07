<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Gère le système de ticketing pour les problèmes utilisateurs
 * Les utilisateurs créent des tickets, les modérateurs les traitent
 */
class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche les tickets de l'utilisateur connecté
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Formulaire de création d'un ticket
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Enregistre un nouveau ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sujet' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'categorie' => 'required|in:bug,contenu,compte,suggestion,autre',
            'priorite' => 'required|in:basse,normale,haute,urgente',
        ]);

        Ticket::create([
            'user_id' => Auth::id(),
            'sujet' => $validated['sujet'],
            'description' => $validated['description'],
            'categorie' => $validated['categorie'],
            'priorite' => $validated['priorite'],
            'statut' => 'ouvert',
        ]);

        return redirect()->route('tickets.index')
            ->with('success', 'Votre ticket a été créé avec succès !');
    }

    /**
     * Affiche le détail d'un ticket
     */
    public function show(Ticket $ticket)
    {
        // Vérifier que l'utilisateur est propriétaire ou modérateur
        if ($ticket->user_id !== Auth::id() && !Auth::user()->hasPermissionTo('moderate avis')) {
            abort(403, 'Vous ne pouvez pas voir ce ticket.');
        }

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Liste tous les tickets (Modérateur uniquement)
     */
    public function adminIndex()
    {
        if (!Auth::user()->hasPermissionTo('moderate avis')) {
            abort(403, 'Accès réservé aux modérateurs.');
        }

        $tickets = Ticket::with('user')
            ->orderByRaw("FIELD(statut, 'ouvert', 'en_cours', 'resolu', 'ferme')")
            ->orderByRaw("FIELD(priorite, 'urgente', 'haute', 'normale', 'basse')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.tickets.index', compact('tickets'));
    }

    /**
     * Prendre en charge un ticket (Modérateur)
     */
    public function assign(Ticket $ticket)
    {
        if (!Auth::user()->hasPermissionTo('moderate avis')) {
            abort(403, 'Action réservée aux modérateurs.');
        }

        $ticket->update([
            'assigned_to' => Auth::id(),
            'statut' => 'en_cours',
        ]);

        return redirect()->back()->with('success', 'Ticket pris en charge.');
    }

    /**
     * Répondre et résoudre un ticket (Modérateur)
     */
    public function respond(Request $request, Ticket $ticket)
    {
        if (!Auth::user()->hasPermissionTo('moderate avis')) {
            abort(403, 'Action réservée aux modérateurs.');
        }

        if ($ticket->user_id === Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Vous ne pouvez pas traiter votre propre ticket.');
        }

        $validated = $request->validate([
            'reponse_moderateur' => 'required|string|max:2000',
            'statut' => 'required|in:en_cours,resolu,ferme',
        ]);

        $updateData = [
            'reponse_moderateur' => $validated['reponse_moderateur'],
            'statut' => $validated['statut'],
        ];

        if (in_array($validated['statut'], ['resolu', 'ferme'])) {
            $updateData['date_resolution'] = now();
        }

        $ticket->update($updateData);

        return redirect()->back()->with('success', 'Réponse envoyée avec succès.');
    }

    /**
     * Fermer un ticket (propriétaire ou modérateur)
     */
    public function close(Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id() && !Auth::user()->hasPermissionTo('moderate avis')) {
            abort(403, 'Vous ne pouvez pas fermer ce ticket.');
        }


        $ticket->update([
            'statut' => 'ferme',
            'date_resolution' => now(),
        ]);

        return redirect()->back()->with('success', 'Ticket fermé.');
    }
}
