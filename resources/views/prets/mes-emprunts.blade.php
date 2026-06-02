@extends('layouts.app')

@section('title', 'Mes emprunts')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="color: var(--accent);">📤 Mes emprunts</h1>
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('prets.demandes-recues') }}" class="btn-primary" style="background-color: var(--warning);">
                    Demandes reçues
                </a>
                <a href="{{ route('prets.mes-prets') }}" class="btn-primary" style="background-color: var(--accent-light);">
                    Mes prêts
                </a>
            </div>
        </div>

        @if($emprunts->count() > 0)
            <div style="display: grid; gap: 1rem;">
                @foreach($emprunts as $emprunt)
                    <div style="background-color: var(--bg-card); border-radius: 10px; padding: 1.5rem; border-left: 4px solid {{ \App\Helpers\PretHelper::couleurStatut($emprunt->statut) }};">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1rem;">
                            <!-- Informations principales -->
                            <div>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Prêteur :</strong> {{ $emprunt->preteur->name }}
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Manga :</strong> {{ $emprunt->tome->manga->titre }}
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Tome :</strong> Tome {{ $emprunt->tome->numero }}
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Statut :</strong> 
                                    <span class="badge" style="background-color: {{ \App\Helpers\PretHelper::couleurStatut($emprunt->statut) }}; color: white;">
                                        {{ $emprunt->statut->label() }}
                                    </span>
                                </p>
                            </div>

                            <!-- Dates -->
                            <div>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Date d'emprunt :</strong> 
                                    @if($emprunt->date_emprunt)
                                        {{ $emprunt->date_emprunt->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Date de retour prévue :</strong> 
                                    @if($emprunt->date_retour_prevue)
                                        {{ $emprunt->date_retour_prevue->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                    <strong>À rendre avant :</strong>
                                    @if($emprunt->date_retour_prevue)
                                        @if($emprunt->date_retour_prevue < now()->toDateString())
                                            <span style="color: var(--accent); font-weight: bold;">⚠ EN RETARD</span>
                                        @else
                                            <span style="color: var(--success);">{{ $emprunt->date_retour_prevue->format('d/m/Y') }}</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-top: 2rem;">
                {{ $emprunts->links() }}
            </div>
        @else
            <div class="empty-state">
                <span class="empty-icon">📤</span>
                <h2>Aucun emprunt en cours</h2>
                <p>Allez à la <a href="{{ route('home') }}">bibliothèque</a> pour emprunter des tomes !</p>
            </div>
        @endif
    </div>
@endsection
