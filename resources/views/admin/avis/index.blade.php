@extends('layouts.app')

@section('title', 'Modération des avis - Modérateur')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <h1 style="color: #60a5fa; margin-bottom: 0.5rem;">
        Avis en attente de modération
        <span style="color: var(--text-secondary); font-size: 0.8em;">({{ $avis->total() }})</span>
    </h1>
    <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 0.9rem;">
        Les avis approuvés seront visibles publiquement sur la page du manga.
    </p>

    @if($avis->isEmpty())
        <div class="empty-state">
            <span class="empty-icon">✅</span>
            <h2>Aucun avis en attente</h2>
            <p>Tous les avis ont été modérés</p>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($avis as $unAvis)
                <div style="background-color: var(--bg-card); border-radius: 10px; padding: 2rem; border-left: 4px solid #60a5fa;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem;">
                                {{ $unAvis->manga->titre }}
                            </h3>
                            <p style="color: var(--text-secondary); font-size: 0.85rem;">
                                par {{ $unAvis->manga->auteur }}
                            </p>
                        </div>
                        <a href="{{ route('mangas.show', $unAvis->manga) }}"
                           class="btn-secondary"
                           target="_blank"
                           style="font-size: 0.85rem;">
                            👁️ Voir le manga
                        </a>
                    </div>

                    <!-- Infos auteur de l'avis -->
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; padding: 0.75rem; background-color: var(--bg-hover); border-radius: 6px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background-color: #60a5fa; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #0f172a; flex-shrink: 0;">
                            {{ substr($unAvis->user->name, 0, 1) }}
                        </div>
                        <div>
                            <strong style="color: var(--text-primary);">{{ $unAvis->user->name }}</strong>
                            <span style="color: var(--text-secondary); font-size: 0.85rem; margin-left: 0.5rem;">
                                — {{ $unAvis->created_at->format('d/m/Y à H:i') }}
                            </span>
                        </div>
                        <div style="margin-left: auto; color: var(--warning); font-size: 1.1rem;">
                            @for($i = 1; $i <= 10; $i++)
                                @if($i <= $unAvis->note) ⭐ @endif
                            @endfor
                            <span style="color: var(--text-secondary); font-size: 0.9rem;">{{ $unAvis->note }}/10</span>
                        </div>
                    </div>

                    <!-- Commentaire -->
                    @if($unAvis->commentaire)
                        <div style="background-color: var(--bg-hover); padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border-left: 3px solid #60a5fa;">
                            <p style="color: var(--text-secondary); font-style: italic; line-height: 1.6; margin: 0;">
                                "{{ $unAvis->commentaire }}"
                            </p>
                        </div>
                    @else
                        <p style="color: var(--text-secondary); font-size: 0.85rem; font-style: italic; margin-bottom: 1.5rem;">
                            Aucun commentaire
                        </p>
                    @endif

                    <!-- Actions -->
                    <div style="display: flex; gap: 1rem;">
                        <form action="{{ route('avis.moderate', $unAvis) }}" method="POST" style="flex: 1;">
                            @csrf
                            <button type="submit"
                                    class="btn-primary"
                                    style="width: 100%; background-color: var(--success);"
                                    onclick="return confirm('Approuver cet avis ? Il sera visible publiquement.')">
                                ✅ Approuver
                            </button>
                        </form>

                        <form action="{{ route('avis.destroy', $unAvis) }}" method="POST" style="flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn-danger"
                                    style="width: 100%;"
                                    onclick="return confirm('Supprimer cet avis définitivement ?')">
                                🗑️ Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination" style="margin-top: 2rem;">
            {{ $avis->links() }}
        </div>
    @endif
</div>
@endsection
