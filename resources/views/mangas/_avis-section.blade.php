<div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border);">
    <h2 style="color: var(--accent); margin-bottom: 1.5rem;">
        Avis de la communauté 
        @if($manga->nombre_avis > 0)
            <span style="color: var(--text-secondary); font-size: 0.9rem;">({{ $manga->nombre_avis }} avis)</span>
        @endif
    </h2>

    @if($manga->nombre_avis > 0)
        <div style="background-color: var(--bg-hover); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="font-size: 3rem; font-weight: bold; color: var(--accent);">
                    {{ $manga->note_moyenne }}
                </div>
                <div>
                    <div style="color: var(--warning); font-size: 1.5rem; margin-bottom: 0.25rem;">
                        @for($i = 1; $i <= 10; $i++)
                            @if($i <= $manga->note_moyenne)
                                ⭐
                            @endif
                        @endfor
                    </div>
                    <div style="color: var(--text-secondary);">sur 10</div>
                </div>
            </div>
        </div>
    @endif

    @auth
        @php
            $userAvis = $manga->avis->where('user_id', Auth::id())->first();
        @endphp

        @if(!$userAvis)
            <!-- Formulaire d'ajout d'avis -->
            <div style="background-color: var(--bg-card); padding: 2rem; border-radius: 8px; margin-bottom: 2rem; border: 2px solid var(--accent);">
                <h3 style="color: var(--text-primary); margin-bottom: 1rem;">Donnez votre avis</h3>
                <form action="{{ route('avis.store', $manga) }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Note *</label>
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                            @for($i = 1; $i <= 10; $i++)
                                <label style="cursor: pointer;">
                                    <input type="radio" name="note" value="{{ $i }}" required style="display: none;" class="note-radio">
                                    <span class="note-star" data-value="{{ $i }}" style="font-size: 1.5rem; color: var(--text-secondary); transition: color 0.2s;">⭐</span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                        <textarea name="commentaire" id="commentaire" class="form-control" rows="4" placeholder="Partagez votre avis sur ce manga..."></textarea>
                    </div>

                    <button type="submit" class="btn-primary">Publier mon avis</button>
                </form>
            </div>
        @else
            <!-- Avis de l'utilisateur -->
            <div style="background-color: var(--bg-hover); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid var(--accent);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h4 style="color: var(--text-primary); margin-bottom: 0.5rem;">Votre avis</h4>
                        <div style="color: var(--warning); margin-bottom: 1rem;">
                            @for($i = 1; $i <= 10; $i++)
                                @if($i <= $userAvis->note)
                                    ⭐
                                @endif
                            @endfor
                            <span style="color: var(--text-secondary); margin-left: 0.5rem;">{{ $userAvis->note }}/10</span>
                        </div>
                        @if($userAvis->commentaire)
                            <p style="color: var(--text-secondary);">{{ $userAvis->commentaire }}</p>
                        @endif
                        @if(!$userAvis->modere)
                            <p style="color: var(--warning); font-size: 0.85rem; margin-top: 0.5rem;">⚠️ En attente de modération</p>
                        @endif
                    </div>
                    <form action="{{ route('avis.destroy', $userAvis) }}" method="POST" onsubmit="return confirm('Supprimer votre avis ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @else
        <div style="background-color: var(--bg-card); padding: 2rem; border-radius: 8px; text-align: center;">
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Connectez-vous pour donner votre avis sur ce manga
            </p>
            <a href="{{ route('login') }}" class="btn-primary">Se connecter</a>
        </div>
    @endauth

    <!-- Liste des avis modérés -->
    @if($manga->avis->where('modere', true)->count() > 0)
        <h3 style="color: var(--text-primary); margin-top: 2rem; margin-bottom: 1rem;">Avis de la communauté</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($manga->avis->where('modere', true)->sortByDesc('created_at') as $avis)
                <div style="background-color: var(--bg-card); padding: 1.5rem; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                        <div>
                            <strong style="color: var(--text-primary);">{{ $avis->user->name }}</strong>
                            <span style="color: var(--text-secondary); font-size: 0.85rem; margin-left: 0.5rem;">
                                {{ $avis->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div style="color: var(--warning);">
                            @for($i = 1; $i <= 10; $i++)
                                @if($i <= $avis->note)
                                    ⭐
                                @endif
                            @endfor
                            <span style="color: var(--text-secondary);">{{ $avis->note }}/10</span>
                        </div>
                    </div>
                    @if($avis->commentaire)
                        <p style="color: var(--text-secondary); line-height: 1.6;">{{ $avis->commentaire }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.note-star {
    transition: color 0.2s;
}
.note-star:hover,
.note-star.active {
    color: var(--warning) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.note-star');
    const radios = document.querySelectorAll('.note-radio');
    
    stars.forEach((star, index) => {
        star.addEventListener('click', function() {
            const value = this.dataset.value;
            radios[index].checked = true;
            
            // Colorier toutes les étoiles jusqu'à celle cliquée
            stars.forEach((s, i) => {
                if (i < value) {
                    s.classList.add('active');
                    s.style.color = 'var(--warning)';
                } else {
                    s.classList.remove('active');
                    s.style.color = 'var(--text-secondary)';
                }
            });
        });
        
        star.addEventListener('mouseenter', function() {
            const value = this.dataset.value;
            stars.forEach((s, i) => {
                if (i < value) {
                    s.style.color = 'var(--warning)';
                }
            });
        });
    });
    
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('mouseleave', function() {
            // Réafficher la sélection active
            const checked = document.querySelector('.note-radio:checked');
            if (checked) {
                const value = checked.value;
                stars.forEach((s, i) => {
                    if (i < value) {
                        s.style.color = 'var(--warning)';
                    } else {
                        s.style.color = 'var(--text-secondary)';
                    }
                });
            } else {
                stars.forEach(s => s.style.color = 'var(--text-secondary)');
            }
        });
    }
});
</script>
