@extends('layouts.app')

@section('title', 'Utilisateur {{ $user->name }} - Admin')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <a href="{{ route('admin.users.index') }}" style="color: var(--text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
        ← Retour à la liste
    </a>

    <div style="background-color: var(--bg-card); border-radius: 10px; padding: 2rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem;">
            <div>
                <h1 style="color: var(--text-primary); margin-bottom: 0.5rem;">{{ $user->name }}</h1>
                <p style="color: var(--text-secondary);">{{ $user->email }}</p>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.5rem;">
                    Inscrit le {{ $user->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <div style="text-align: right;">
                @php
                    $role = $roles->first() ?? 'user';
                @endphp
                <span class="badge" style="font-size: 1rem; padding: 0.5rem 1rem; background-color:
                    @if($role == 'admin') var(--warning)
                    @elseif($role == 'moderator') var(--info, #3498db)
                    @else var(--bg-hover)
                    @endif;
                    @if($role == 'admin') color: #000; @endif">
                    {{ ucfirst($role) }}
                </span>
            </div>
        </div>

        @if($user->id !== Auth::id())
            <div style="background-color: var(--bg-hover); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: var(--text-primary); margin-bottom: 1rem;">Changer le rôle</h3>
                <form action="{{ route('admin.users.update-role', $user) }}" method="POST" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    @csrf
                    <select name="role" class="form-control" style="width: auto;">
                        <option value="user" {{ $role == 'user' ? 'selected' : '' }}>Utilisateur</option>
                        <option value="moderator" {{ $role == 'moderator' ? 'selected' : '' }}>Modérateur</option>
                        <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Administrateur</option>
                    </select>
                    <button type="submit" class="btn-primary">
                        Mettre à jour le rôle
                    </button>
                </form>
            </div>
        @endif
    </div>

    <h2 style="color: var(--accent); margin-bottom: 1.5rem;">
        Bibliothèque de l'utilisateur
        <span style="color: var(--text-secondary); font-size: 0.8em;">({{ $mangas->count() }} mangas)</span>
    </h2>

    @if($mangas->isEmpty())
        <div class="empty-state">
            <span class="empty-icon">📚</span>
            <p>Cet utilisateur n'a pas encore de mangas.</p>
        </div>
    @else
        <table style="width: 100%; border-collapse: collapse; background-color: var(--bg-card); border-radius: 10px; overflow: hidden;">
            <thead>
                <tr style="background-color: var(--bg-hover);">
                    <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Titre</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Auteur</th>
                    <th style="padding: 1rem; text-align: center; color: var(--text-primary);">Statut</th>
                    <th style="padding: 1rem; text-align: center; color: var(--text-primary);">Public</th>
                    <th style="padding: 1rem; text-align: center; color: var(--text-primary);">Note moyenne</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Ajouté le</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mangas as $manga)
                    <tr style="border-bottom: 1px solid var(--bg-hover);">
                        <td style="padding: 1rem; color: var(--text-primary); font-weight: 600;">
                            <a href="{{ route('mangas.show', $manga) }}" style="color: var(--accent); text-decoration: none;">
                                {{ $manga->titre }}
                            </a>
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary);">
                            {{ $manga->auteur }}
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            <span class="badge badge-{{ $manga->statut }}">
                                @if($manga->statut == 'en_cours') En cours
                                @elseif($manga->statut == 'termine') Terminé
                                @else Abandonné
                                @endif
                            </span>
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            @if($manga->est_public)
                                <span style="color: var(--success);">✓ Public</span>
                            @else
                                <span style="color: var(--text-secondary);">Privé</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: center; color: var(--warning);">
                            @if($manga->nombre_avis > 0)
                                ⭐ {{ number_format($manga->note_moyenne, 1) }}/10
                                <span style="color: var(--text-secondary); font-size: 0.8rem;">
                                    ({{ $manga->nombre_avis }} avis)
                                </span>
                            @else
                                <span style="color: var(--text-secondary);">-</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary);">
                            {{ $manga->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
