@extends('layouts.app')
@section('title', $title ?? 'Ma Collection')
@section('content')
<div class="page-header">
    <h1>{{ $title ?? 'Ma Collection' }}</h1>
    @if(auth()->user()->hasRole('user'))
        <p class="subtitle">Vos mangas privés ({{ $mangas->total() }})</p>
    @endif
    @if(auth()->user()->hasRole('admin'))
        <p class="subtitle">Tous les mangas de la base ({{ $mangas->total() }})</p>
    @endif
    <a href="{{ route('mangas.create') }}" class="btn-primary">+ Ajouter un manga</a>
</div>
@if($mangas->isEmpty())
    <div class="empty-state">
        <span class="empty-icon">📚</span>
        <h2>Votre collection est vide</h2>
        <p>Commencez par ajouter votre premier manga !</p>
        <a href="{{ route('mangas.create') }}" class="btn-primary">Ajouter votre premier manga</a>
    </div>
@else
    <div class="manga-grid">
        @foreach($mangas as $manga)
            <div class="manga-card">
                <div class="manga-cover" style="
                    @if($manga->image_couverture)
                        background-image: url('{{ asset('storage/' . $manga->image_couverture) }}');
                        background-size: cover;
                        background-position: center;
                    @endif
                ">
                    @if(!$manga->image_couverture)
                        <span class="manga-icon">📖</span>
                    @endif
                </div>
                <div class="manga-info">
                    <h3>{{ $manga->titre }}</h3>
                    <p class="manga-author">par {{ $manga->auteur }}</p>
                    
                    @if($manga->description)
                        <p class="manga-description">{{ Str::limit($manga->description, 100) }}</p>
                    @endif
                    <div class="manga-meta">
                        <span class="badge badge-{{ $manga->statut }}">
                            @if($manga->statut === 'en_cours') En cours
                            @elseif($manga->statut === 'termine') Terminé
                            @else Abandonné
                            @endif
                        </span>
                        
                        @if($manga->note)
                            <span class="manga-rating">
                                ⭐ {{ $manga->note }}/10
                            </span>
                        @endif
                        @if($manga->est_public)
                            <span class="badge badge-public">Public</span>
                        @else
                            <span class="badge badge-private">Privé</span>
                        @endif
                    </div>
                    @if(auth()->user()->hasRole('admin') && $manga->user)
                        <p class="manga-owner">Propriétaire : {{ $manga->user->name }}</p>
                    @endif
                    <div class="manga-actions">
                        <a href="{{ route('mangas.show', $manga) }}" class="btn-secondary">Voir</a>
                        @can('update', $manga)
                            <a href="{{ route('mangas.edit', $manga) }}" class="btn-warning">Modifier</a>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- Pagination -->
    <div class="pagination">
        {{ $mangas->links() }}
    </div>
@endif
@endsection
