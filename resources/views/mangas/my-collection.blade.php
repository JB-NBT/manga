@extends('layouts.app')
@section('title', $title ?? 'Ma Collection')
@section('content')
<div class="page-header">
    <h1>{{ $title ?? 'Ma Collection' }}</h1>
    <p class="subtitle">{{ $mangas->total() }} manga(s)</p>
    <a href="{{ route('mangas.create') }}" class="btn-primary">Ajouter un manga</a>
</div>

@if($mangas->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">M</div>
        <h2>Collection vide</h2>
        <p>Commencez par ajouter votre premier manga.</p>
        <a href="{{ route('mangas.create') }}" class="btn-primary">Ajouter un manga</a>
    </div>
@else
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
                        @if($manga->est_public)
                            <div class="manga-rating-badge">
                                <span class="value">Public</span>
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
                        @if(auth()->user()->hasAnyRole(['admin', 'moderator']) && $manga->user)
                            <span>{{ $manga->user->name }}</span>
                        @else
                            <span>{{ $manga->nombre_tomes }} tome(s)</span>
                        @endif
                        @if($manga->note)
                            <span class="rating">{{ $manga->note }}/10</span>
                        @endif
                    </div>
                    <div class="manga-card-actions">
                        <a href="{{ route('mangas.show', $manga) }}" class="btn-card btn-details">Voir</a>
                        @can('update', $manga)
                            <a href="{{ route('mangas.edit', $manga) }}" class="btn-card btn-read">Modifier</a>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="pagination">
        {{ $mangas->links() }}
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
    margin-bottom: 0.5rem;
}
.subtitle {
    color: var(--text-secondary);
    margin-bottom: 1rem;
}
</style>
@endsection
