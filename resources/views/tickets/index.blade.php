@extends('layouts.app')

@section('title', 'Mes Tickets - Manga Library')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--accent);">
            Mes Tickets
            <span style="color: var(--text-secondary); font-size: 0.8em;">({{ $tickets->total() }})</span>
        </h1>
        <a href="{{ route('tickets.create') }}" class="btn-primary">
            + Nouveau ticket
        </a>
    </div>

    @if($tickets->isEmpty())
        <div class="empty-state">
            <span class="empty-icon">🎫</span>
            <h2>Aucun ticket</h2>
            <p>Vous n'avez pas encore créé de ticket de support.</p>
            <a href="{{ route('tickets.create') }}" class="btn-primary" style="margin-top: 1rem;">
                Créer un ticket
            </a>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($tickets as $ticket)
                <a href="{{ route('tickets.show', $ticket) }}" style="text-decoration: none;">
                    <div style="background-color: var(--bg-card); border-radius: 10px; padding: 1.5rem;
                                border-left: 4px solid
                                @if($ticket->statut == 'ouvert') var(--warning)
                                @elseif($ticket->statut == 'en_cours') var(--info, #3498db)
                                @elseif($ticket->statut == 'resolu') var(--success)
                                @else var(--text-secondary)
                                @endif;
                                transition: transform 0.2s, box-shadow 0.2s;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">
                                    {{ $ticket->sujet }}
                                </h3>
                                <p style="color: var(--text-secondary); font-size: 0.85rem;">
                                    Créé le {{ $ticket->created_at->format('d/m/Y à H:i') }}
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
                                <span class="badge" style="background-color:
                                    @if($ticket->priorite == 'urgente') var(--error)
                                    @elseif($ticket->priorite == 'haute') var(--warning)
                                    @else var(--bg-hover)
                                    @endif;">
                                    {{ ucfirst($ticket->priorite) }}
                                </span>
                            </div>
                        </div>
                        <p style="color: var(--text-secondary); margin-top: 0.5rem; font-size: 0.9rem;">
                            {{ Str::limit($ticket->description, 100) }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pagination" style="margin-top: 2rem;">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection
