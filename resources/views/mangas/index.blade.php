@extends('layouts.app')
@section('title', 'Accueil - MangaLibrary')
@section('content')
    <div class="page-header">
        <h1>Bibliotheque</h1>
        @if(request('search'))
            <p class="search-info">Resultats pour "{{ request('search') }}" ({{ $mangas->total() }} manga(s))</p>
        @endif
    </div>

    @if($mangas->count() > 0)
        <div class="manga-grid">
            @foreach($mangas as $manga)
                <div class="manga-card">
                    <a href="{{ route('mangas.show', $manga) }}" class="manga-card-link">
                        <div class="manga-cover" style="@if($manga->image_couverture) background-image: url('{{ asset('storage/' . $manga->image_couverture) }}'); @endif">
                            @if(!$manga->image_couverture)
                                <span class="manga-placeholder-icon">M</span>
                            @endif
                            <span class="manga-status-badge badge-{{ $manga->statut }}">
                                @if($manga->statut == 'en_cours') En cours
                                @elseif($manga->statut == 'termine') Termine
                                @else Abandonne
                                @endif
                            </span>
                            @if($manga->nombre_avis > 0)
                                <div class="manga-rating-badge">
                                    <span class="star">*</span>
                                    <span class="value">{{ number_format($manga->note_moyenne, 1) }}/10</span>
                                </div>
                            @endif
                        </div>
                    </a>
                    <div class="manga-card-body">
                        <a href="{{ route('mangas.show', $manga) }}" style="text-decoration: none;">
                            <h3 class="manga-card-title">{{ $manga->titre }}</h3>
                        </a>
                        <p class="manga-card-author">{{ $manga->auteur }}</p>
                        <div class="manga-card-meta">
                            <span>{{ $manga->user->name }}</span>
                            <span>{{ $manga->nombre_tomes }} tome(s)</span>
                        </div>
                        <div class="manga-card-actions">
                            <a href="{{ route('mangas.show', $manga) }}" class="btn-card btn-details">Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination">
            {{ $mangas->appends(request()->query())->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">M</div>
            <h2>Aucun manga trouve</h2>
            @if(request('search'))
                <p>Aucun resultat pour "{{ request('search') }}"</p>
                <a href="{{ route('home') }}" class="btn-primary">Voir tous les mangas</a>
            @else
                <p>La bibliotheque est vide pour le moment.</p>
                @auth
                    @can('create manga')
                        <a href="{{ route('mangas.create') }}" class="btn-primary">Ajouter un manga</a>
                    @endcan
                @endauth
            @endif
        </div>
    @endif

<style>
.page-header {
    text-align: center;
    margin-bottom: 2rem;
}
.page-header h1 {
    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.search-info {
    color: var(--text-secondary);
    margin-top: 0.5rem;
}
</style>
@endsection
