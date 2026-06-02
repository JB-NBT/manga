@extends('layouts.app')

@section('title', 'Mes prêts')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="color: var(--accent);">📥 Mes prêts</h1>
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('prets.demandes-recues') }}" class="btn-primary" style="background-color: var(--warning);">
                    Demandes reçues
                </a>
                <a href="{{ route('prets.mes-emprunts') }}" class="btn-primary" style="background-color: var(--accent-light);">
                    Mes emprunts
                </a>
            </div>
        </div>

        @if($prets->count() > 0)
            <div style="display: grid; gap: 1rem;">
                @foreach($prets as $pret)
                    <div style="background-color: var(--bg-card); border-radius: 10px; padding: 1.5rem; border-left: 4px solid {{ \App\Helpers\PretHelper::couleurStatut($pret->statut) }};">
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem; margin-bottom: 1rem; align-items: start;">
                            <!-- Informations principales -->
                            <div>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Emprunteur :</strong> {{ $pret->emprunteur->name }}
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Manga :</strong> {{ $pret->tome->manga->titre }}
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Tome :</strong> Tome {{ $pret->tome->numero }}
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                    <strong>Statut :</strong> 
                                    <span class="badge" style="background-color: {{ \App\Helpers\PretHelper::couleurStatut($pret->statut) }}; color: white;">
                                        {{ $pret->statut->label() }}
                                    </span>
                                </p>
                            </div>

                            <!-- Dates -->
                            <div>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Date de prêt :</strong> 
                                    @if($pret->date_emprunt)
                                        {{ $pret->date_emprunt->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Retour prévu :</strong> 
                                    @if($pret->date_retour_prevue)
                                        {{ $pret->date_retour_prevue->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                    <strong>Retour effectif :</strong>
                                    @if($pret->date_retour_effective)
                                        {{ $pret->date_retour_effective->format('d/m/Y') }}
                                    @else
                                        <span style="color: var(--accent);">En attente...</span>
                                    @endif
                                </p>
                            </div>

                            <!-- Actions -->
                            <div>
                                @if($pret->statut === 'en_cours' && !$pret->date_retour_effective)
                                    <form action="{{ route('prets.restituer', $pret) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-primary" style="width: 100%; background-color: var(--success);">
                                            ↩ Marquer comme restitué
                                        </button>
                                    </form>
                                @elseif($pret->date_retour_effective)
                                    <p style="color: var(--success); font-weight: bold; text-align: center;">
                                        ✓ Restitué le {{ $pret->date_retour_effective->format('d/m/Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-top: 2rem;">
                {{ $prets->links() }}
            </div>
        @else
            <div class="empty-state">
                <span class="empty-icon">📥</span>
                <h2>Aucun prêt en cours</h2>
                <p>Vous n'avez pas encore prêté de tomes.</p>
            </div>
        @endif
    </div>
@endsection
