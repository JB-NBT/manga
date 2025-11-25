@extends('layouts.app')

@section('title', 'Gestion des mangas - Copyright')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <h1 style="color: var(--accent); margin-bottom: 2rem;">
        🛡️ Gestion Protection Copyright
    </h1>

    <!-- Mangas expirés -->
    <div style="background-color: rgba(239, 71, 111, 0.1); padding: 2rem; border-radius: 10px; border-left: 4px solid var(--error); margin-bottom: 2rem;">
        <h2 style="color: var(--error); margin-bottom: 1rem;">
            ❌ Mangas retirés (expirés)
            <span style="font-size: 0.8em; color: var(--text-secondary);">({{ $expiredMangas->count() }})</span>
        </h2>
        
        @if($expiredMangas->isEmpty())
            <p style="color: var(--text-secondary);">Aucun manga retiré pour le moment.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($expiredMangas as $manga)
                    <div style="background-color: var(--bg-card); padding: 1.5rem; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">{{ $manga->titre }}</h3>
                            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                par {{ $manga->auteur }} • Ajouté par {{ $manga->user->name }}
                            </p>
                            <p style="color: var(--error); font-size: 0.85rem; margin-top: 0.5rem;">
                                ⏰ Retiré le {{ $manga->updated_at->format('d/m/Y') }}
                            </p>
                        </div>
                        
                        <div style="display: flex; gap: 1rem;">
                            <a href="{{ route('mangas.show', $manga) }}" class="btn-secondary">
                                👁️ Voir
                            </a>
                            <form action="{{ route('mangas.republish', $manga) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-primary" style="background-color: var(--success);">
                                    ♻️ Republier
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Mangas bientôt expirés -->
    <div style="background-color: rgba(255, 209, 102, 0.1); padding: 2rem; border-radius: 10px; border-left: 4px solid var(--warning); margin-bottom: 2rem;">
        <h2 style="color: var(--warning); margin-bottom: 1rem;">
            ⚠️ Mangas bientôt expirés (30 jours)
            <span style="font-size: 0.8em; color: var(--text-secondary);">({{ $expiringSoonMangas->count() }})</span>
        </h2>
        
        @if($expiringSoonMangas->isEmpty())
            <p style="color: var(--text-secondary);">Aucun manga proche de l'expiration.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($expiringSoonMangas as $manga)
                    @php
                        $dateRef = $manga->date_derniere_republication ?? $manga->created_at;
                        $daysRemaining = now()->diffInDays($dateRef->addYear());
                    @endphp
                    <div style="background-color: var(--bg-card); padding: 1.5rem; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">{{ $manga->titre }}</h3>
                            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                par {{ $manga->auteur }} • Ajouté par {{ $manga->user->name }}
                            </p>
                            <p style="color: var(--warning); font-size: 0.85rem; margin-top: 0.5rem;">
                                ⏰ Expire dans {{ $daysRemaining }} jour(s)
                            </p>
                        </div>
                        
                        <div style="display: flex; gap: 1rem;">
                            <a href="{{ route('mangas.show', $manga) }}" class="btn-secondary">
                                👁️ Voir
                            </a>
                            <form action="{{ route('mangas.republish', $manga) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-primary" style="background-color: var(--success);">
                                    ♻️ Republier maintenant
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Bouton retour -->
    <a href="{{ route('admin.publication.index') }}" class="btn-secondary">
        ← Retour aux demandes de publication
    </a>
</div>
@endsection
