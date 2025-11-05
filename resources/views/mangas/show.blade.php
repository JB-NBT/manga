@extends('layouts.app')

@section('title', $manga->titre . ' - Manga Library')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="background-color: var(--bg-card); border-radius: 10px; overflow: hidden;">
            <div class="manga-cover" style="height: 400px; background-size: cover; background-position: center; background-image: url('{{ $manga->image_couverture ? asset('storage/' . $manga->image_couverture) : '' }}');">
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

        {{-- Section avis (si manga public) --}}
        @if($manga->est_public)
            @include('mangas._avis-section')
        @endif
    </div>
@endsection
