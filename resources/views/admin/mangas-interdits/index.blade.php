@extends('layouts.app')

@section('title', 'Mangas Interdits - Admin')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--accent);">
            Contenus interdits
            <span style="color: var(--text-secondary); font-size: 0.8em;">({{ $mangasInterdits->total() }})</span>
        </h1>
        <a href="{{ route('admin.mangas-interdits.create') }}" class="btn-primary">
            + Ajouter un contenu interdit
        </a>
    </div>

    {{-- Alerte si des mangas en infraction sont détectés --}}
    @if(count($mangasEnInfraction) > 0)
    <div style="background-color: rgba(231, 76, 60, 0.15); border: 2px solid #e74c3c; border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="color: #e74c3c; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 1.5rem;">⚠️</span>
            {{ count($mangasEnInfraction) }} manga(s) en infraction détecté(s)
        </h3>
        <div style="max-height: 200px; overflow-y: auto;">
            @foreach($mangasEnInfraction as $infraction)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: rgba(0,0,0,0.2); border-radius: 5px; margin-bottom: 0.5rem;">
                <div>
                    <strong style="color: var(--text-primary);">{{ $infraction['manga']->titre }}</strong>
                    <span style="color: var(--text-secondary);"> par {{ $infraction['proprietaire']->name }}</span>
                    <br>
                    <small style="color: #e74c3c;">Correspond à : {{ $infraction['interdit']->titre }} ({{ $infraction['interdit']->categorie_libelle }})</small>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('mangas.show', $infraction['manga']) }}" class="btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                        Voir
                    </a>
                    @can('delete any manga')
                    <form action="{{ route('mangas.destroy', $infraction['manga']) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;"
                                onclick="return confirm('Supprimer ce manga et avertir l\'utilisateur ?')">
                            Supprimer
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filtres par catégorie --}}
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.mangas-interdits.index') }}"
           class="btn-secondary" style="font-size: 0.85rem; {{ !request('categorie') ? 'background: var(--accent); color: white;' : '' }}">
            Toutes
        </a>
        @foreach($categories as $key => $label)
        <a href="{{ route('admin.mangas-interdits.index', ['categorie' => $key]) }}"
           class="btn-secondary" style="font-size: 0.85rem; {{ request('categorie') == $key ? 'background: var(--accent); color: white;' : '' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @if($mangasInterdits->isEmpty())
        <div class="empty-state">
            <span class="empty-icon">🚫</span>
            <h2>Aucun contenu interdit</h2>
            <p>La liste des contenus interdits est vide. Ajoutez des mots-clés ou titres à surveiller.</p>
        </div>
    @else
        <table style="width: 100%; border-collapse: collapse; background-color: var(--bg-card); border-radius: 10px; overflow: hidden;">
            <thead>
                <tr style="background-color: var(--bg-hover);">
                    <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Titre/Mot-clé</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Catégorie</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Raison</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Ajouté par</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-primary);">Date</th>
                    <th style="padding: 1rem; text-align: center; color: var(--text-primary);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mangasInterdits as $manga)
                    <tr style="border-bottom: 1px solid var(--bg-hover);">
                        <td style="padding: 1rem; color: var(--text-primary); font-weight: 600;">
                            {{ $manga->titre }}
                            @if($manga->auteur)
                                <br><small style="color: var(--text-secondary); font-weight: normal;">{{ $manga->auteur }}</small>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 500;
                                @switch($manga->categorie)
                                    @case('hentai')
                                    @case('pedophile')
                                        background: rgba(231, 76, 60, 0.2); color: #e74c3c;
                                        @break
                                    @case('gore')
                                        background: rgba(155, 89, 182, 0.2); color: #9b59b6;
                                        @break
                                    @case('incitation_haine')
                                    @case('terrorisme')
                                        background: rgba(230, 126, 34, 0.2); color: #e67e22;
                                        @break
                                    @case('copyright')
                                        background: rgba(52, 152, 219, 0.2); color: #3498db;
                                        @break
                                    @default
                                        background: rgba(149, 165, 166, 0.2); color: #95a5a6;
                                @endswitch
                            ">
                                {{ $manga->categorie_libelle }}
                            </span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary); max-width: 250px;">
                            {{ Str::limit($manga->raison, 60) }}
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary);">
                            {{ $manga->ajoutePar->name ?? 'Système' }}
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary);">
                            {{ $manga->date_interdiction->format('d/m/Y') }}
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            <form action="{{ route('admin.mangas-interdits.destroy', $manga) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;"
                                        onclick="return confirm('Retirer ce contenu de la liste des interdits ?')">
                                    Retirer
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination" style="margin-top: 2rem;">
            {{ $mangasInterdits->appends(request()->query())->links() }}
        </div>
    @endif

    {{-- Légende des catégories --}}
    <div style="margin-top: 2rem; padding: 1.5rem; background-color: var(--bg-card); border-radius: 10px;">
        <h3 style="color: var(--text-primary); margin: 0 0 1rem 0;">Catégories de contenus interdits</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
            @foreach($categories as $key => $label)
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="width: 12px; height: 12px; border-radius: 50%;
                    @switch($key)
                        @case('hentai')
                        @case('pedophile')
                            background: #e74c3c;
                            @break
                        @case('gore')
                            background: #9b59b6;
                            @break
                        @case('incitation_haine')
                        @case('terrorisme')
                            background: #e67e22;
                            @break
                        @case('copyright')
                            background: #3498db;
                            @break
                        @default
                            background: #95a5a6;
                    @endswitch
                "></span>
                <span style="color: var(--text-secondary); font-size: 0.9rem;">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
