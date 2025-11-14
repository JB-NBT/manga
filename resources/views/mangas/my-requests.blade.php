@extends('layouts.app')

@section('title', 'Mes demandes de publication')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <h1 style="color: var(--accent); margin-bottom: 2rem;">Mes demandes de publication</h1>

    @if($requests->isEmpty())
        <div class="empty-state">
            <span class="empty-icon">📋</span>
            <h2>Aucune demande</h2>
            <p>Vous n'avez pas encore fait de demande de publication</p>
            <a href="{{ route('mangas.my-collection') }}" class="btn-primary">
                Voir ma collection
            </a>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($requests as $request)
                <div style="background-color: var(--bg-card); border-radius: 10px; padding: 2rem; 
                            border-left: 4px solid 
                            {{ $request->statut == 'en_attente' ? 'var(--warning)' : ($request->statut == 'approuve' ? 'var(--success)' : 'var(--error)') }}">
                    
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">
                                {{ $request->manga->titre }}
                            </h3>
                            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                Demande envoyée le {{ $request->created_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                        
                        <!-- Badge statut -->
                        @if($request->statut == 'en_attente')
                            <span class="badge" style="background-color: var(--warning); color: #000;">
                                ⏳ En attente
                            </span>
                        @elseif($request->statut == 'approuve')
                            <span class="badge badge-public">
                                ✅ Approuvé
                            </span>
                        @else
                            <span class="badge" style="background-color: var(--error); color: white;">
                                ❌ Refusé
                            </span>
                        @endif
                    </div>

                    @if($request->message_utilisateur)
                        <div style="background-color: var(--bg-hover); padding: 1rem; border-radius: 6px; margin: 1rem 0;">
                            <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">
                                Votre message :
                            </p>
                            <p style="color: var(--text-secondary); font-size: 0.9rem; font-style: italic;">
                                "{{ $request->message_utilisateur }}"
                            </p>
                        </div>
                    @endif

                    @if($request->message_admin && $request->statut != 'en_attente')
                        <div style="background-color: {{ $request->statut == 'approuve' ? 'rgba(6, 214, 160, 0.1)' : 'rgba(239, 71, 111, 0.1)' }}; 
                                    padding: 1rem; border-radius: 6px; margin: 1rem 0;
                                    border-left: 3px solid {{ $request->statut == 'approuve' ? 'var(--success)' : 'var(--error)' }}">
                            <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">
                                Message de l'administrateur :
                            </p>
                            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                {{ $request->message_admin }}
                            </p>
                        </div>
                    @endif

                    @if($request->date_traitement)
                        <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 1rem;">
                            Traité le {{ $request->date_traitement->format('d/m/Y à H:i') }}
                        </p>
                    @endif

                    <div style="margin-top: 1rem;">
                        <a href="{{ route('mangas.show', $request->manga) }}" class="btn-secondary">
                            Voir le manga
                        </a>
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
