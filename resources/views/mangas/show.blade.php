@extends('layouts.app')

@section('title', $manga->titre . ' - Manga Library')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="background-color: var(--bg-card); border-radius: 10px; overflow: hidden;">
            <div class="manga-cover" style="height: 400px; background-size: contain; background-position: center top; background-repeat: no-repeat; background-image: url('{{ $manga->image_couverture ? asset('storage/' . $manga->image_couverture) : '' }}');">
                @if(!$manga->image_couverture)
                    📖
                @endif
            </div>
            
            <div style="padding: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h1 style="color: var(--accent); margin-bottom: 0.5rem;">{{ $manga->titre }}</h1>
                        <p style="color: var(--text-secondary); font-size: 1.1rem;">par {{ $manga->auteur }}</p>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.5rem;">
                            Ajouté par {{ $manga->user->name }}
                        </p>
                    </div>
                    
                    @can('update', $manga)
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="{{ route('mangas.edit', $manga) }}" class="btn-primary" style="background-color: var(--warning); color: #000;">
                                Modifier
                            </a>
                            <form action="{{ route('mangas.destroy', $manga) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce manga ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-primary" style="background-color: var(--accent);">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>

                <div style="display: flex; gap: 1rem; margin: 1.5rem 0; flex-wrap: wrap;">
                    <span class="badge badge-{{ $manga->statut }}">
                        @if($manga->statut == 'en_cours') En cours
                        @elseif($manga->statut == 'termine') Terminé
                        @else Abandonné
                        @endif
                    </span>
                    
                    <span style="color: var(--text-secondary);">
                        📚 {{ $manga->nombre_tomes }} tome(s)
                    </span>
                    
                    @if($manga->note)
                        <span class="rating">⭐ {{ $manga->note }}/10</span>
                    @endif

                    @if($manga->est_public)
                        <span class="badge badge-public">Public</span>
                    @else
                        <span class="badge badge-private">Privé</span>
                    @endif
                </div>

                {{-- DEMANDE DE PUBLICATION (si user propriétaire et manga privé) --}}
                @auth
                    @if(Auth::id() === $manga->user_id && !$manga->est_public)
                        @php
                            $existingRequest = $manga->publicationRequest()
                                ->where('statut', 'en_attente')
                                ->first();
                        @endphp

                        @if(!$existingRequest)
                            <div style="background-color: var(--bg-hover); padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0; border-left: 4px solid var(--accent);">
                                <h3 style="color: var(--text-primary); margin-bottom: 1rem;">📢 Rendre ce manga public</h3>
                                <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                                    Soumettez une demande pour publier ce manga dans la bibliothèque communautaire.
                                </p>
                                <form action="{{ route('publication.request', $manga) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="message_utilisateur" class="form-label">Message pour l'admin (optionnel)</label>
                                        <textarea name="message_utilisateur" 
                                                  id="message_utilisateur" 
                                                  class="form-control" 
                                                  rows="3" 
                                                  placeholder="Pourquoi souhaitez-vous publier ce manga ?"></textarea>
                                    </div>
                                    <button type="submit" class="btn-primary">
                                        📤 Demander la publication
                                    </button>
                                </form>
                            </div>
                        @else
                            <div style="background-color: rgba(255, 209, 102, 0.2); padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0; border-left: 4px solid var(--warning);">
                                <p style="color: var(--text-primary); font-weight: bold;">
                                    ⏳ Demande de publication en attente
                                </p>
                                <p style="color: var(--text-secondary); margin-top: 0.5rem;">
                                    Votre demande est en cours de traitement par un administrateur.
                                </p>
                            </div>
                        @endif
                    @endif
                @endauth

                @if($manga->description)
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">Description</h3>
                        <p style="color: var(--text-secondary); line-height: 1.8;">{{ $manga->description }}</p>
                    </div>
                @endif

                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">
                        Ajouté le {{ $manga->created_at->format('d/m/Y à H:i') }}
                    </p>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <a href="{{ Auth::check() && Auth::id() == $manga->user_id ? route('mangas.my-collection') : route('home') }}" class="btn-primary">
                        ← Retour
                    </a>
                    
                    {{-- Lien vers les tomes --}}
                    @if(Auth::check() && (Auth::id() === $manga->user_id || Auth::user()->hasRole('admin')))
                        <a href="{{ route('tomes.index', $manga) }}" class="btn-primary" style="background-color: var(--success);">
                            📚 Gérer les tomes
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Section preview (si manga public) --}}
        @if($manga->est_public)
            @php $previews = $manga->previews; @endphp

            {{-- Bouton preview pour les utilisateurs connectés --}}
            @auth
                @if($previews->count() > 0)
                    <div style="margin-top: 2rem; padding: 1.5rem; background-color: var(--bg-card); border-radius: 10px; border-top: 1px solid var(--border);">
                        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">Aperçu du manga</h3>
                        <button onclick="document.getElementById('previewModal-{{ $manga->id }}').style.display='flex'" class="btn-primary" style="background-color: var(--accent-light);">
                            Voir l'aperçu ({{ $previews->count() }} page{{ $previews->count() > 1 ? 's' : '' }})
                        </button>
                    </div>

                    {{-- Modal preview --}}
                    <div id="previewModal-{{ $manga->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; justify-content:center; align-items:center;" onclick="if(event.target===this)this.style.display='none'">
                        <div style="background:var(--bg-card); border-radius:12px; padding:2rem; max-width:900px; width:95%; max-height:90vh; overflow-y:auto; position:relative;">
                            <button onclick="document.getElementById('previewModal-{{ $manga->id }}').style.display='none'" style="position:absolute; top:1rem; right:1rem; background:var(--accent); color:#fff; border:none; border-radius:50%; width:2rem; height:2rem; cursor:pointer; font-size:1rem; line-height:1;">✕</button>
                            <h2 style="color:var(--accent); margin-bottom:1.5rem;">Aperçu — {{ $manga->titre }}</h2>
                            <div style="display:flex; gap:1rem; flex-wrap:wrap; justify-content:center;">
                                @foreach($previews as $preview)
                                    <div style="text-align:center;">
                                        <p style="color:var(--text-secondary); margin-bottom:0.5rem; font-size:0.9rem;">Page {{ $preview->ordre }}</p>
                                        <img src="{{ asset('storage/' . $preview->image_path) }}" alt="Preview page {{ $preview->ordre }}" class="preview-img" style="max-width:380px; width:100%; border-radius:8px; border:2px solid var(--border); cursor:zoom-in;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div style="margin-top: 2rem; padding: 1rem; background-color: var(--bg-hover); border-radius: 8px; color: var(--text-secondary); font-size:0.9rem;">
                        Aucun aperçu disponible pour ce manga.
                    </div>
                @endif
            @endauth

            {{-- Gestion des previews (Admin/Modérateur) --}}
            @can('uploadPreview', $manga)
                <div style="margin-top: 2rem; padding: 1.5rem; background-color: var(--bg-card); border-radius: 10px; border-left: 4px solid var(--warning);">
                    <h3 style="color: var(--text-primary); margin-bottom: 1.5rem;">Gestion des images de preview</h3>

                    @foreach([1, 2] as $ordre)
                        @php $preview = $previews->firstWhere('ordre', $ordre); @endphp
                        <div style="margin-bottom: 1.5rem; padding: 1rem; background-color: var(--bg-hover); border-radius: 8px;">
                            <p style="color: var(--text-primary); font-weight: bold; margin-bottom: 0.75rem;">Page {{ $ordre }}</p>
                            @if($preview)
                                <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                                    <img src="{{ asset('storage/' . $preview->image_path) }}" alt="Preview {{ $ordre }}" style="height:100px; border-radius:6px; border:1px solid var(--border);">
                                    <form action="{{ route('mangas.preview.delete', [$manga, $preview]) }}" method="POST" onsubmit="return confirm('Supprimer cette image de preview ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-primary" style="background-color: var(--accent);">Supprimer</button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('mangas.preview.upload', $manga) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="ordre" value="{{ $ordre }}">
                                    <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                                        <input type="file" name="image" accept="image/*" required class="form-control" style="max-width:300px;">
                                        <button type="submit" class="btn-primary" style="background-color: var(--success);">Uploader page {{ $ordre }}</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endcan
        @endif

        {{-- Section avis (si manga public) --}}
        @if($manga->est_public)
            @include('mangas._avis-section')
        @endif
    </div>

    {{-- Lightbox fullscreen --}}
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
@endsection
