@extends('layouts.app')

@section('title', 'Tomes de ' . $manga->titre)

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="color: var(--accent); margin-bottom: 0.5rem;">Tomes de {{ $manga->titre }}</h1>
            <p style="color: var(--text-secondary);">{{ $manga->nombre_tomes }} tome(s) au total</p>
        </div>

        @if($manga->user_id === Auth::id() || Auth::user()->hasRole('admin'))
            @if($tomes->isEmpty())
                <form action="{{ route('tomes.generate', $manga) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary">
                        Générer les tomes
                    </button>
                </form>
            @else
                <a href="{{ route('mangas.show', $manga) }}" class="btn-primary" style="background-color: var(--bg-hover);">
                    ← Retour au manga
                </a>
            @endif
        @endif
    </div>

    @if($tomes->isEmpty())
        <div class="empty-state">
            <span class="empty-icon">📚</span>
            <h2>Aucun tome créé</h2>
            <p>Générez automatiquement les {{ $manga->nombre_tomes }} tomes pour ce manga</p>
        </div>
    @else
        <!-- Statistiques -->
        @php
            $tomesPos = $tomes->where('possede', true)->count();
            $pourcentage = $manga->nombre_tomes > 0 ? round(($tomesPos / $manga->nombre_tomes) * 100) : 0;
        @endphp

        <div style="background-color: var(--bg-card); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="color: var(--text-primary);">Progression</h3>
                <span style="color: var(--accent); font-weight: bold; font-size: 1.2rem;">
                    {{ $tomesPos }} / {{ $manga->nombre_tomes }} tomes ({{ $pourcentage }}%)
                </span>
            </div>

            <!-- Barre de progression -->
            <div style="background-color: var(--bg-dark); height: 20px; border-radius: 10px; overflow: hidden;">
                <div style="background: linear-gradient(90deg, var(--accent), var(--success)); height: 100%; width: {{ $pourcentage }}%; transition: width 0.3s ease;"></div>
            </div>
        </div>

        <!-- Grille des tomes -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
            @foreach($tomes as $tome)
                <div class="tome-card" style="
                    background-color: var(--bg-card);
                    border-radius: 8px;
                    padding: 1rem;
                    text-align: center;
                    border: 2px solid {{ $tome->possede ? 'var(--success)' : 'var(--border)' }};
                    transition: all 0.3s ease;
                    cursor: pointer;
                " onclick="toggleTome({{ $tome->id }})">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                        {{ $tome->possede ? '✅' : '📖' }}
                    </div>
                    <h4 style="color: var(--text-primary); margin-bottom: 0.25rem;">
                        Tome {{ $tome->numero }}
                    </h4>
                    <p style="color: var(--text-secondary); font-size: 0.85rem;">
                        {{ $tome->possede ? 'Possédé' : 'Non possédé' }}
                    </p>

                    @if($tome->possede && $tome->date_achat)
                        <p style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.5rem;">
                            Acheté le {{ $tome->date_achat->format('d/m/Y') }}
                        </p>
                    @endif

                    @if($manga->user_id === Auth::id() || Auth::user()->hasRole('admin'))
                        <!-- Formulaire caché pour toggle -->
                        <form id="toggle-form-{{ $tome->id }}"
                              action="{{ route('tomes.toggle', $tome) }}"
                              method="POST"
                              style="display: none;">
                            @csrf
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.tome-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}
</style>

<script>
function toggleTome(tomeId) {
    @if($manga->user_id === Auth::id() || Auth::user()->hasRole('admin'))
        document.getElementById('toggle-form-' + tomeId).submit();
    @endif
}
</script>
@endsection
