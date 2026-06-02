@extends('layouts.app')

@section('title', 'Demandes de prêt reçues')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="color: var(--accent);">📥 Demandes de prêt reçues</h1>
            <a href="{{ route('prets.mes-prets') }}" class="btn-primary" style="background-color: var(--accent-light);">
                Voir mes prêts
            </a>
        </div>

        @if($demandes->count() > 0)
            <div style="display: grid; gap: 1rem;">
                @foreach($demandes as $demande)
                    <div style="background-color: var(--bg-card); border-radius: 10px; padding: 1.5rem; border-left: 4px solid var(--warning);">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
                            <!-- Informations principales -->
                            <div>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Demandeur :</strong> {{ $demande->emprunteur->name }}
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Manga :</strong> {{ $demande->tome->manga->titre }}
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <strong>Tome :</strong> Tome {{ $demande->tome->numero }}
                                </p>
                                <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                    <strong>Date de demande :</strong> {{ $demande->date_demande->format('d/m/Y') }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div>
                                <h3 style="color: var(--text-primary); margin-bottom: 1rem; font-size: 1rem;">Répondre à la demande</h3>

                                <!-- Accepter -->
                                <form action="{{ route('prets.accepter', $demande) }}" method="POST" style="margin-bottom: 1rem;">
                                    @csrf
                                    <div class="form-group" style="margin-bottom: 0.8rem;">
                                        <label for="date_retour_prevue_{{ $demande->id }}" class="form-label" style="font-size: 0.9rem;">
                                            Date de retour prévue *
                                        </label>
                                        <input type="date" 
                                               name="date_retour_prevue" 
                                               id="date_retour_prevue_{{ $demande->id }}" 
                                               class="form-control" 
                                               required 
                                               min="{{ now()->addDays(1)->format('Y-m-d') }}">
                                    </div>
                                    <button type="submit" class="btn-primary" style="width: 100%; background-color: var(--success); margin-bottom: 0.5rem;">
                                        ✓ Accepter le prêt
                                    </button>
                                </form>

                                <!-- Refuser -->
                                <button onclick="toggleRefuserForm({{ $demande->id }})" class="btn-primary" style="width: 100%; background-color: var(--accent);">
                                    ✕ Refuser
                                </button>

                                <!-- Formulaire refus (caché) -->
                                <div id="refuser-form-{{ $demande->id }}" style="display: none; margin-top: 1rem; padding: 1rem; background-color: var(--bg-hover); border-radius: 8px;">
                                    <form action="{{ route('prets.refuser', $demande) }}" method="POST">
                                        @csrf
                                        <div class="form-group" style="margin-bottom: 0.8rem;">
                                            <label for="motif_{{ $demande->id }}" class="form-label" style="font-size: 0.9rem;">
                                                Motif du refus *
                                            </label>
                                            <textarea name="motif_refus" 
                                                      id="motif_{{ $demande->id }}" 
                                                      class="form-control" 
                                                      rows="3" 
                                                      placeholder="Expliquez pourquoi vous refusez ce prêt..."
                                                      required></textarea>
                                        </div>
                                        <button type="submit" class="btn-primary" style="width: 100%; background-color: var(--accent);">
                                            Confirmer le refus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-top: 2rem;">
                {{ $demandes->links() }}
            </div>
        @else
            <div class="empty-state">
                <span class="empty-icon">📥</span>
                <h2>Aucune demande de prêt</h2>
                <p>Vous n'avez pas encore de demande de prêt en attente.</p>
            </div>
        @endif
    </div>

    <script>
    function toggleRefuserForm(id) {
        const form = document.getElementById('refuser-form-' + id);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
    </script>
@endsection
