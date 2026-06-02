@extends('layouts.app')
@section('title', 'Accueil - MangaLibrary')
@section('content')
    <div class="page-header">
        <h1>Ma Bibliothèque</h1>
        @if(request('search'))
            <p class="search-info">Résultats pour "{{ request('search') }}" ({{ $mangas->total() }} manga(s))</p>
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
                                @elseif($manga->statut == 'termine') Terminé
                                @else Abandonné
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

                        {{-- Affichage des statuts de tomes partagés --}}
                        @php
                            $tomesPartages = $manga->tomes->filter(function($t) { return $t->partage; });
                        @endphp
                        @if($tomesPartages->count() > 0)
                            <div style="margin: 0.8rem 0; padding: 0.6rem; background-color: rgba(16, 185, 129, 0.1); border-radius: 6px;">
                                <p style="color: var(--text-secondary); font-size: 0.75rem; margin-bottom: 0.4rem; font-weight: bold;">
                                    📚 Tomes disponibles au prêt :
                                </p>
                                <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                                    @foreach($tomesPartages as $tome)
                                        <span class="badge" style="
                                            background-color: {{ \App\Helpers\PretHelper::couleurStatut($tome->statut_pret) }};
                                            color: white;
                                            padding: 0.3rem 0.6rem;
                                            border-radius: 4px;
                                            font-size: 0.75rem;
                                            font-weight: bold;
                                        ">
                                            T{{ $tome->numero }}:
                                            @switch($tome->statut_pret)
                                                @case('disponible')
                                                    ✓
                                                    @break
                                                @case('demande')
                                                    ⏳
                                                    @break
                                                @case('prete')
                                                    📤
                                                    @break
                                                @case('restitue')
                                                    ↩
                                                    @break
                                            @endswitch
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="manga-card-actions">
                            <a href="{{ route('mangas.show', $manga) }}" class="btn-card btn-details">Details</a>
                            @auth
                                @if($manga->previews->count() > 0)
                                    <button onclick="document.getElementById('previewModal-{{ $manga->id }}').style.display='flex'" class="btn-card btn-preview">
                                        Preview
                                    </button>
                                @endif
                                @if($tomesPartages->count() > 0)
                                    <button onclick="document.getElementById('empruntModal-{{ $manga->id }}').style.display='flex'" class="btn-card" style="background-color: var(--success); color: white;">
                                        Emprunter
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
                                            <img src="{{ asset('storage/' . $preview->image_path) }}" alt="Preview page {{ $preview->ordre }}" class="preview-img" style="max-width:380px; width:100%; border-radius:8px; border:2px solid var(--border); cursor:zoom-in;">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth

                @auth
                    @if($tomesPartages->count() > 0)
                        <div id="empruntModal-{{ $manga->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; justify-content:center; align-items:center;" onclick="if(event.target===this)this.style.display='none'">
                            <div style="background:var(--bg-card); border-radius:12px; padding:2rem; max-width:500px; width:95%; position:relative;">
                                <button onclick="document.getElementById('empruntModal-{{ $manga->id }}').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:var(--accent); color:#fff; border:none; border-radius:50%; width:2rem; height:2rem; cursor:pointer; font-size:1rem; line-height:1;">X</button>
                                
                                <h2 style="color:var(--accent); margin-bottom:1.5rem;">Emprunter un tome</h2>
                                <p style="color:var(--text-secondary); margin-bottom:1.5rem;">{{ $manga->titre }} - de {{ $manga->auteur }}</p>
                                
                                <p style="color:var(--text-secondary); margin-bottom:1rem; font-size:0.9rem;">Selectionnez un tome :</p>
                                
                                <div style="display: grid; gap: 0.8rem; max-height: 300px; overflow-y: auto;">
                                    @php
                                        $tomesDisponibles = $tomesPartages->where('statut_pret', 'disponible');
                                    @endphp
                                    
                                    @if($tomesDisponibles->count() > 0)
                                        @foreach($tomesDisponibles as $tome)
                                            <form action="{{ route('prets.store', $tome) }}" method="POST" style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: center; padding: 1rem; background-color: var(--bg-hover); border-radius: 8px; border: 2px solid var(--border);">
                                                @csrf
                                                <div>
                                                    <p style="color: var(--text-primary); font-weight: bold; margin-bottom: 0.25rem;">Tome {{ $tome->numero }}</p>
                                                    <p style="color: var(--text-secondary); font-size: 0.85rem;">
                                                        <span style="background-color: #10b981; color: white; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 0.75rem;">
                                                            Disponible
                                                        </span>
                                                    </p>
                                                </div>
                                                <button type="submit" class="btn-primary" style="background-color: var(--success); color: white; padding: 0.5rem 1rem; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                                                    Demander
                                                </button>
                                            </form>
                                        @endforeach
                                    @else
                                        <p style="color: var(--text-secondary); text-align: center; padding: 1.5rem;">Aucun tome disponible actuellement</p>
                                    @endif
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
            <h2>Aucun manga trouvé</h2>
            @if(request('search'))
                <p>Aucun résultat pour "{{ request('search') }}"</p>
                <a href="{{ route('home') }}" class="btn-primary">Voir tous les mangas</a>
            @else
                <p>La bibliothèque est vide pour le moment.</p>
                @auth
                    @can('create manga')
                        <a href="{{ route('mangas.create') }}" class="btn-primary">Ajouter un manga</a>
                    @endcan
                @endauth
            @endif
        </div>
    @endif

{{-- Lightbox fullscreen (partagé) --}}
@auth
<div id="lightbox" onclick="this.style.display='none'" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.95); z-index:99999; justify-content:center; align-items:center; cursor:zoom-out;">
    <img id="lightbox-img" src="" alt="" style="max-width:95vw; max-height:95vh; border-radius:6px; object-fit:contain;">
    <button onclick="document.getElementById('lightbox').style.display='none'" style="position:fixed; top:1rem; right:1rem; background:rgba(255,255,255,0.15); color:#fff; border:none; border-radius:50%; width:2.5rem; height:2.5rem; cursor:pointer; font-size:1.2rem; line-height:1;">✕</button>
</div>
<script>
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('preview-img')) {
        e.stopPropagation();
        document.getElementById('lightbox-img').src = e.target.src;
        document.getElementById('lightbox').style.display = 'flex';
    }
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') document.getElementById('lightbox').style.display = 'none';
});
</script>
@endauth

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
