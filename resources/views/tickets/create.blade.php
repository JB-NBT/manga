@extends('layouts.app')

@section('title', 'Nouveau Ticket - Manga Library')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <h1 style="color: var(--accent); margin-bottom: 2rem;">Créer un ticket</h1>

    <form action="{{ route('tickets.store') }}" method="POST" style="background-color: var(--bg-card); padding: 2rem; border-radius: 10px;">
        @csrf

        <div class="form-group">
            <label for="sujet" class="form-label">Sujet *</label>
            <input type="text"
                   name="sujet"
                   id="sujet"
                   class="form-control @error('sujet') is-invalid @enderror"
                   value="{{ old('sujet') }}"
                   placeholder="Résumez votre problème"
                   required>
            @error('sujet')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="categorie" class="form-label">Catégorie *</label>
            <select name="categorie" id="categorie" class="form-control @error('categorie') is-invalid @enderror" required>
                <option value="">Sélectionnez une catégorie</option>
                <option value="bug" {{ old('categorie') == 'bug' ? 'selected' : '' }}>Bug / Problème technique</option>
                <option value="contenu" {{ old('categorie') == 'contenu' ? 'selected' : '' }}>Problème de contenu</option>
                <option value="compte" {{ old('categorie') == 'compte' ? 'selected' : '' }}>Problème de compte</option>
                <option value="suggestion" {{ old('categorie') == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                <option value="autre" {{ old('categorie') == 'autre' ? 'selected' : '' }}>Autre</option>
            </select>
            @error('categorie')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="priorite" class="form-label">Priorité *</label>
            <select name="priorite" id="priorite" class="form-control @error('priorite') is-invalid @enderror" required>
                <option value="basse" {{ old('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                <option value="normale" {{ old('priorite', 'normale') == 'normale' ? 'selected' : '' }}>Normale</option>
                <option value="haute" {{ old('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                <option value="urgente" {{ old('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
            </select>
            @error('priorite')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description *</label>
            <textarea name="description"
                      id="description"
                      class="form-control @error('description') is-invalid @enderror"
                      rows="6"
                      placeholder="Décrivez votre problème en détail..."
                      required>{{ old('description') }}</textarea>
            @error('description')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn-primary" style="flex: 1;">
                Envoyer le ticket
            </button>
            <a href="{{ route('tickets.index') }}" class="btn-secondary">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
