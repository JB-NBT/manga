<?php

namespace App\Http\Controllers;

use App\Models\Pret;
use App\Models\Tome;
use App\Enums\StatutEmprunt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PretController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche les demandes de prêt reçues (prêts que je dois accepter/refuser).
     */
    public function demandesRecues()
    {
        $user = Auth::user();
        $demandes = Pret::where('preteur_id', $user->id)
            ->where('statut', 'demande')
            ->with(['emprunteur', 'tome.manga'])
            ->orderBy('date_demande', 'desc')
            ->paginate(12);

        return view('prets.demandes-recues', compact('demandes'));
    }

    /**
     * Affiche les emprunts en cours de l'utilisateur.
     */
    public function mesEmprunts()
    {
        $user = Auth::user();
        $emprunts = Pret::where('emprunteur_id', $user->id)
            ->whereIn('statut', ['accepte', 'en_cours'])
            ->with(['preteur', 'tome.manga'])
            ->orderBy('date_emprunt', 'desc')
            ->paginate(12);

        return view('prets.mes-emprunts', compact('emprunts'));
    }

    /**
     * Affiche les prêts que j'ai donnés.
     */
    public function mesPrets()
    {
        $user = Auth::user();
        $prets = Pret::where('preteur_id', $user->id)
            ->whereIn('statut', ['accepte', 'en_cours'])
            ->with(['emprunteur', 'tome.manga'])
            ->orderBy('date_emprunt', 'desc')
            ->paginate(12);

        return view('prets.mes-prets', compact('prets'));
    }

    /**
     * Crée une demande de prêt pour un tome.
     */
    public function store(Request $request, Tome $tome)
    {
        $this->authorize('emprunter', $tome);

        $user = Auth::user();

        // Vérifier si une demande existe déjà
        $existant = Pret::where('tome_id', $tome->id)
            ->where('emprunteur_id', $user->id)
            ->whereIn('statut', ['demande', 'accepte', 'en_cours'])
            ->exists();

        if ($existant) {
            return back()->with('error', 'Vous avez déjà une demande active pour ce tome.');
        }

        // Vérifier que c'est pas l'owner du tome
        if ($tome->manga->user_id === $user->id) {
            return back()->with('error', 'Vous ne pouvez pas emprunter votre propre tome.');
        }

        // Créer la demande de prêt
        Pret::create([
            'preteur_id' => $tome->manga->user_id,
            'emprunteur_id' => $user->id,
            'tome_id' => $tome->id,
            'statut' => 'demande',
            'date_demande' => now()->toDateString(),
        ]);

        return back()->with('success', 'Demande de prêt envoyée avec succès !');
    }

    /**
     * Accepte une demande de prêt.
     */
    public function accepter(Request $request, Pret $pret)
    {
        $this->authorize('accepter', $pret);

        $validated = $request->validate([
            'date_retour_prevue' => 'required|date|after:today',
        ]);

        $pret->update([
            'statut' => 'en_cours',
            'date_emprunt' => now()->toDateString(),
            'date_retour_prevue' => $validated['date_retour_prevue'],
        ]);

        // Mettre à jour le statut du tome
        $pret->tome->update(['statut_pret' => 'prete']);

        return back()->with('success', 'Prêt accepté !');
    }

    /**
     * Refuse une demande de prêt.
     */
    public function refuser(Request $request, Pret $pret)
    {
        $this->authorize('refuser', $pret);

        $validated = $request->validate([
            'motif_refus' => 'required|string|max:500',
        ]);

        $pret->update([
            'statut' => 'refuse',
            'motif_refus' => $validated['motif_refus'],
        ]);

        return back()->with('success', 'Demande refusée.');
    }

    /**
     * Marque un prêt comme restitué.
     */
    public function restituer(Request $request, Pret $pret)
    {
        $this->authorize('restituer', $pret);

        $pret->update([
            'statut' => 'restitue',
            'date_retour_effective' => now()->toDateString(),
        ]);

        // Mettre à jour le statut du tome
        $pret->tome->update(['statut_pret' => 'disponible']);

        return back()->with('success', 'Tome marqué comme restitué !');
    }

    /**
     * Annule une demande de prêt (par l'emprunteur).
     */
    public function annulerDemande(Pret $pret)
    {
        $this->authorize('annulerDemande', $pret);

        if ($pret->statut !== 'demande') {
            return back()->with('error', 'Seules les demandes en attente peuvent être annulées.');
        }

        $pret->delete();

        return back()->with('success', 'Demande annulée.');
    }
}
