@extends('layouts.app')

@section('title', 'Ajouter un Manga Interdit - Admin')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <a href="{{ route('admin.mangas-interdits.index') }}" style="color: var(--text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
        ← Retour à la liste
    </a>

    <h1 style="color: var(--accent); margin-bottom: 2rem;">Ajouter un manga interdit</h1>

    <div style="background-color: rgba(231, 76, 60, 0.1); border: 1px solid rgba(231, 76, 60, 0.3); border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
        <p style="color: #e74c3c; margin: 0; font-size: 0.9rem;">
            <strong>Note :</strong> Ajoutez ici les titres ou mots-clés interdits. Les mangas correspondants seront signalés automatiquement.
        </p>
    </div>

    <form action="{{ route('admin.mangas-interdits.store') }}" method="POST" style="background-color: var(--bg-card); padding: 2rem; border-radius: 10px;">
        @csrf

        <div class="form-group">
            <label for="titre" class="form-label">Titre ou mot-clé interdit *</label>
            <input type="text"
                   name="titre"
                   id="titre"
                   class="form-control @error('titre') is-invalid @enderror"
                   value="{{ old('titre') }}"
                   placeholder="Ex: hentai, gore, etc."
                   required>
            <small style="color: var(--text-secondary);">Peut être un titre exact ou un mot-clé générique</small>
            @error('titre')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="auteur" class="form-label">Auteur (optionnel)</label>
            <input type="text"
                   name="auteur"
                   id="auteur"
                   class="form-control @error('auteur') is-invalid @enderror"
                   value="{{ old('auteur') }}"
                   placeholder="Laisser vide pour un mot-clé générique">
            @error('auteur')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="categorie" class="form-label">Catégorie *</label>
            <select name="categorie"
                    id="categorie"
                    class="form-control @error('categorie') is-invalid @enderror"
                    required>
                <option value="">-- Sélectionner une catégorie --</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ old('categorie') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('categorie')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="raison" class="form-label">Raison de l'interdiction *</label>
            <textarea name="raison"
                      id="raison"
                      class="form-control @error('raison') is-invalid @enderror"
                      rows="4"
                      placeholder="Expliquez pourquoi ce contenu est interdit..."
                      required>{{ old('raison') }}</textarea>
            @error('raison')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn-primary" style="flex: 1;">
                Ajouter à la liste
            </button>
            <a href="{{ route('admin.mangas-interdits.index') }}" class="btn-secondary">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
