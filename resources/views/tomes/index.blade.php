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

                        <!-- Bouton pour ajouter lien de lecture -->
                        @if($tome->possede)
                            <button onclick="event.stopPropagation(); showLinkModal({{ $tome->id }}, '{{ $tome->url_lecture ?? '' }}')" 
                                    class="btn-secondary" 
                                    style="margin-top: 0.5rem; font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                {{ $tome->url_lecture ? '🔗 Modifier lien' : '🔗 Ajouter lien' }}
                            </button>
                        @endif
                    @else
                        @if($tome->url_lecture)
                            <a href="{{ $tome->url_lecture }}" 
                               target="_blank" 
                               class="btn-primary" 
                               style="margin-top: 0.5rem; font-size: 0.75rem; padding: 0.25rem 0.5rem;"
                               onclick="event.stopPropagation();">
                                📖 Lire
                            </a>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Modal pour ajouter un lien de lecture -->
<div id="linkModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background-color: var(--bg-card); padding: 2rem; border-radius: 10px; max-width: 500px; width: 90%;">
        <h3 style="color: var(--accent); margin-bottom: 1rem;">Ajouter un lien de lecture</h3>
        <form id="linkForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="possede" value="1">
            <input type="hidden" name="date_achat" id="dateAchat">
            
            <div class="form-group">
                <label for="url_lecture" class="form-label">URL du site de lecture</label>
                <input type="url" 
                       name="url_lecture" 
                       id="url_lecture" 
                       class="form-control" 
                       placeholder="https://exemple.com/manga/chapitre-1">
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn-primary" style="flex: 1;">Enregistrer</button>
                <button type="button" onclick="closeLinkModal()" class="btn-secondary" style="flex: 1;">Annuler</button>
            </div>
        </form>
    </div>
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

function showLinkModal(tomeId, currentUrl) {
    const modal = document.getElementById('linkModal');
    const form = document.getElementById('linkForm');
    const input = document.getElementById('url_lecture');
    
    form.action = '/tomes/' + tomeId;
    input.value = currentUrl;
    
    modal.style.display = 'flex';
}

function closeLinkModal() {
    document.getElementById('linkModal').style.display = 'none';
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('linkModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLinkModal();
    }
});
</script>
@endsection
