@extends('layouts.app')

@section('title', 'Accueil - Manga Library')

@section('content')
    <h1 style="text-align: center; margin-bottom: 2rem; color: var(--accent);">
        Découvrez les mangas de la communauté
    </h1>

    @if($mangas->count() > 0)
        <div class="manga-grid">
            @foreach($mangas as $manga)
                <a href="{{ route('mangas.show', $manga) }}" style="text-decoration: none;">
                    <div class="manga-card">
                        <div class="manga-cover">
                            📖
                        </div>
                        <div class="manga-info">
                            <h3 class="manga-title">{{ $manga->titre }}</h3>
                            <p class="manga-author">par {{ $manga->auteur }}</p>
                            <p style="color: var(--text-secondary); font-size: 0.85rem;">
                                Ajouté par {{ $manga->user->name }}
                            </p>
                            <div class="manga-meta">
                                <span class="badge badge-{{ $manga->statut }}">
                                    @if($manga->statut == 'en_cours') En cours
                                    @elseif($manga->statut == 'termine') Terminé
                                    @else Abandonné
                                    @endif
                                </span>
                                @if($manga->note)
                                    <span class="rating">⭐ {{ $manga->note }}/10</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pagination">
            {{ $mangas->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 4rem; color: var(--text-secondary);">
            <p style="font-size: 3rem; margin-bottom: 1rem;">📚</p>
            <p>Aucun manga pour le moment.</p>
            @auth
                @can('create manga')
                    <a href="{{ route('mangas.create') }}" class="btn-primary" style="margin-top: 1rem;">
                        Ajouter le premier manga
                    </a>
                @endcan
            @endauth
        </div>
    @endif
@endsection
