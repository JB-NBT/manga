@extends('layouts.app')

@section('title', 'Ticket #{{ $ticket->id }} - Manga Library')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <a href="{{ route('tickets.index') }}" style="color: var(--text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
        ← Retour à mes tickets
    </a>

    <div style="background-color: var(--bg-card); border-radius: 10px; padding: 2rem;
                border-left: 4px solid
                @if($ticket->statut == 'ouvert') var(--warning)
                @elseif($ticket->statut == 'en_cours') var(--info, #3498db)
                @elseif($ticket->statut == 'resolu') var(--success)
                @else var(--text-secondary)
                @endif;">

        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
            <div>
                <h1 style="color: var(--text-primary); margin-bottom: 0.5rem;">
                    {{ $ticket->sujet }}
                </h1>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">
                    Ticket #{{ $ticket->id }} - Créé le {{ $ticket->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <span class="badge" style="background-color:
                    @if($ticket->statut == 'ouvert') var(--warning)
                    @elseif($ticket->statut == 'en_cours') var(--info, #3498db)
                    @elseif($ticket->statut == 'resolu') var(--success)
                    @else var(--text-secondary)
                    @endif; color: #000;">
                    {{ ucfirst(str_replace('_', ' ', $ticket->statut)) }}
                </span>
                <span class="badge" style="background-color: var(--bg-hover);">
                    {{ ucfirst($ticket->categorie) }}
                </span>
                <span class="badge" style="background-color:
                    @if($ticket->priorite == 'urgente') var(--error)
                    @elseif($ticket->priorite == 'haute') var(--warning)
                    @else var(--bg-hover)
                    @endif;">
                    {{ ucfirst($ticket->priorite) }}
                </span>
            </div>
        </div>

        <div style="background-color: var(--bg-hover); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">Description</h3>
            <p style="color: var(--text-secondary); white-space: pre-line;">{{ $ticket->description }}</p>
        </div>

        @if($ticket->reponse_moderateur)
            <div style="background-color: rgba(6, 214, 160, 0.1); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--success); margin-bottom: 1.5rem;">
                <h3 style="color: var(--success); margin-bottom: 1rem;">
                    Réponse du modérateur
                    @if($ticket->assignedTo)
                        <span style="font-weight: normal; font-size: 0.9rem;">
                            ({{ $ticket->assignedTo->name }})
                        </span>
                    @endif
                </h3>
                <p style="color: var(--text-secondary); white-space: pre-line;">{{ $ticket->reponse_moderateur }}</p>
                @if($ticket->date_resolution)
                    <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 1rem;">
                        Résolu le {{ $ticket->date_resolution->format('d/m/Y à H:i') }}
                    </p>
                @endif
            </div>
        @endif

        @if($ticket->statut !== 'ferme')
            <form action="{{ route('tickets.close', $ticket) }}" method="POST" style="margin-top: 1.5rem;">
                @csrf
                <button type="submit" class="btn-secondary" onclick="return confirm('Fermer ce ticket ?')">
                    Fermer le ticket
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
