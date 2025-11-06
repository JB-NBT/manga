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
                        <div style="display: flex; gap: 0.5rem;">
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

                <div style="display: flex; gap: 1rem; margin: 1.5rem 0;">
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
                </div>

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

                <div style="margin-top: 2rem;">
                    <a href="{{ Auth::check() && Auth::id() == $manga->user_id ? route('mangas.my-collection') : route('home') }}" class="btn-primary">
                        ← Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
