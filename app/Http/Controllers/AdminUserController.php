<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Manga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Gestion des utilisateurs par l'administrateur
 * Interface simple sans graphismes
 */
class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Liste tous les utilisateurs avec leurs statistiques
     */
    public function index()
    {
        if (!Auth::user()->hasPermissionTo('manage users')) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        $users = User::withCount('mangas')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Affiche le détail d'un utilisateur et sa bibliothèque
     */
    public function show(User $user)
    {
        if (!Auth::user()->hasPermissionTo('manage users')) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        $mangas = Manga::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = $user->getRoleNames();

        return view('admin.users.show', compact('user', 'mangas', 'roles'));
    }

    /**
     * Change le rôle d'un utilisateur
     */
    public function updateRole(Request $request, User $user)
    {
        if (!Auth::user()->hasPermissionTo('manage users')) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        // Empêcher de modifier son propre rôle
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }

        $validated = $request->validate([
            'role' => 'required|in:user,moderator,admin',
        ]);

        // Retirer tous les rôles actuels et assigner le nouveau
        $user->syncRoles([$validated['role']]);

        return redirect()->back()->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur et toutes ses données
     */
    public function destroy(User $user)
    {
        if (!Auth::user()->hasPermissionTo('manage users')) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        // Empêcher de se supprimer soi-même
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Les mangas seront supprimés en cascade grâce aux foreign keys
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
