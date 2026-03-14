@extends('layouts.app')

@section('title', 'Demandes de publication - Modérateur')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <h1 style="color: var(--accent); margin-bottom: 2rem;">
        Demandes de publication en attente
        <span style="color: var(--text-secondary); font-size: 0.8em;">({{ $requests->total() }})</span>
    </h1>

    @if($requests->isEmpty())
        <div class="empty-state">
            <span class="empty-icon">✅</span>
            <h2>Aucune demande en attente</h2>
            <p>Toutes les demandes de publication ont été traitées</p>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($requests as $request)
                <div style="background-color: var(--bg-card); border-radius: 10px; padding: 2rem; border-left: 4px solid var(--warning);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">
                                {{ $request->manga->titre }}
                            </h3>
                            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                par {{ $request->manga->auteur }}
                            </p>
                            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.5rem;">
                                Demandé par <strong>{{ $request->user->name }}</strong> 
                                le {{ $request->created_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                        
                        <a href="{{ route('mangas.show', $request->manga) }}" 
                           class="btn-secondary" 
                           target="_blank"
                           style="font-size: 0.85rem;">
                            👁️ Voir le manga
                        </a>
                    </div>

                    @if($request->message_utilisateur)
                        <div style="background-color: var(--bg-hover); padding: 1rem; border-radius: 6px; margin: 1rem 0;">
                            <p style="color: var(--text-secondary); font-size: 0.9rem; font-style: italic;">
                                "{{ $request->message_utilisateur }}"
                            </p>
                        </div>
                    @endif

                    <!-- Infos du manga -->
                    <div style="display: flex; gap: 1rem; margin: 1rem 0; flex-wrap: wrap;">
                        <span class="badge badge-{{ $request->manga->statut }}">
                            @if($request->manga->statut == 'en_cours') En cours
                            @elseif($request->manga->statut == 'termine') Terminé
                            @else Abandonné
                            @endif
                        </span>
                        <span style="color: var(--text-secondary);">
                            📚 {{ $request->manga->nombre_tomes }} tome(s)
                        </span>
                        @if($request->manga->note)
                            <span class="rating">⭐ {{ $request->manga->note }}/10</span>
                        @endif
                    </div>

                    <!-- Actions admin -->
                    <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                        <!-- Formulaire d'approbation -->
                        <form action="{{ route('admin.publication.approve', $request) }}" 
                              method="POST" 
                              style="flex: 1;">
                            @csrf
                            <input type="text" 
                                   name="message_admin" 
                                   class="form-control" 
                                   placeholder="Message pour l'utilisateur (optionnel)" 
                                   style="margin-bottom: 0.5rem;">
                            <button type="submit" 
                                    class="btn-primary" 
                                    style="width: 100%; background-color: var(--success);"
                                    onclick="return confirm('Approuver cette demande ?')">
                                ✅ Approuver
                            </button>
                        </form>

                        <!-- Formulaire de refus -->
                        <form action="{{ route('admin.publication.reject', $request) }}" 
                              method="POST" 
                              style="flex: 1;">
                            @csrf
                            <input type="text" 
                                   name="message_admin" 
                                   class="form-control" 
                                   placeholder="Raison du refus *" 
                                   required
                                   style="margin-bottom: 0.5rem;">
                            <button type="submit" 
                                    class="btn-danger" 
                                    style="width: 100%;"
                                    onclick="return confirm('Refuser cette demande ?')">
                                ❌ Refuser
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination" style="margin-top: 2rem;">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
