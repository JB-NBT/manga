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
                            @auth
                                @if($manga->previews->count() > 0)
                                    <button onclick="document.getElementById('previewModal-{{ $manga->id }}').style.display='flex'" class="btn-card btn-preview">
                                        Preview
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                {{-- Modal preview pour la carte (users connectés seulement) --}}
                @auth
                    @if($manga->previews->count() > 0)
                        <div id="previewModal-{{ $manga->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; justify-content:center; align-items:center;" onclick="if(event.target===this)this.style.display='none'">
                            <div style="background:var(--bg-card); border-radius:12px; padding:2rem; max-width:900px; width:95%; max-height:90vh; overflow-y:auto; position:relative;">
                                <button onclick="document.getElementById('previewModal-{{ $manga->id }}').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:var(--accent); color:#fff; border:none; border-radius:50%; width:2rem; height:2rem; cursor:pointer; font-size:1rem; line-height:1;">✕</button>
                                <h2 style="color:var(--accent); margin-bottom:1.5rem;">Aperçu — {{ $manga->titre }}</h2>
                                <div style="display:flex; gap:1rem; flex-wrap:wrap; justify-content:center;">
                                    @foreach($manga->previews as $preview)
                                        <div style="text-align:center;">
                                            <p style="color:var(--text-secondary); margin-bottom:0.5rem; font-size:0.9rem;">Page {{ $preview->ordre }}</p>
                                            <img src="{{ asset('storage/' . $preview->image_path) }}" alt="Preview page {{ $preview->ordre }}" style="max-width:380px; width:100%; border-radius:8px; border:2px solid var(--border);">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth
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
.btn-preview {
    background-color: var(--accent-light, #7c3aed);
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 0.85rem;
    padding: 0.4rem 0.9rem;
    border-radius: 6px;
    transition: opacity 0.2s;
}
.btn-preview:hover {
    opacity: 0.85;
}
</style>
@endsection
