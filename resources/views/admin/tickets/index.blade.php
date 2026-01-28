@extends('layouts.app')

@section('title', 'Gestion des Tickets - Admin')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <h1 style="color: var(--accent); margin-bottom: 2rem;">
        Gestion des tickets
        <span style="color: var(--text-secondary); font-size: 0.8em;">({{ $tickets->total() }})</span>
    </h1>

    @if($tickets->isEmpty())
        <div class="empty-state">
            <span class="empty-icon">✅</span>
            <h2>Aucun ticket</h2>
            <p>Aucun ticket de support à traiter.</p>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($tickets as $ticket)
                <div style="background-color: var(--bg-card); border-radius: 10px; padding: 1.5rem;
                            border-left: 4px solid
                            @if($ticket->statut == 'ouvert') var(--warning)
                            @elseif($ticket->statut == 'en_cours') var(--info, #3498db)
                            @elseif($ticket->statut == 'resolu') var(--success)
                            @else var(--text-secondary)
                            @endif;">

                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">
                                #{{ $ticket->id }} - {{ $ticket->sujet }}
                            </h3>
                            <p style="color: var(--text-secondary); font-size: 0.85rem;">
                                Par <strong>{{ $ticket->user->name }}</strong> ({{ $ticket->user->email }})
                                - {{ $ticket->created_at->format('d/m/Y à H:i') }}
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
                            <span class="badge" style="background-color: var(--bg-hover);">
                                {{ ucfirst($ticket->categorie) }}
                            </span>
                        </div>
                    </div>

                    <div style="background-color: var(--bg-hover); padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                        <p style="color: var(--text-secondary); font-size: 0.9rem;">
                            {{ Str::limit($ticket->description, 200) }}
                        </p>
                    </div>

                    @if($ticket->assigned_to)
                        <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1rem;">
                            Assigné à : <strong>{{ $ticket->assignedTo->name }}</strong>
                        </p>
                    @endif

                    @if($ticket->statut !== 'ferme')
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            @if(!$ticket->assigned_to)
                                <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-secondary" style="font-size: 0.85rem;">
                                        Prendre en charge
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.tickets.respond', $ticket) }}" method="POST" style="flex: 1; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                @csrf
                                <input type="text"
                                       name="reponse_moderateur"
                                       class="form-control"
                                       placeholder="Réponse..."
                                       required
                                       style="flex: 1; min-width: 200px;">
                                <select name="statut" class="form-control" style="width: auto;">
                                    <option value="en_cours">En cours</option>
                                    <option value="resolu">Résolu</option>
                                    <option value="ferme">Fermé</option>
                                </select>
                                <button type="submit" class="btn-primary" style="font-size: 0.85rem;">
                                    Répondre
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="pagination" style="margin-top: 2rem;">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection
