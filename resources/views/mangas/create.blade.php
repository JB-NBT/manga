@extends('layouts.app')

@section('title', 'Ajouter un manga')

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="color: var(--accent); margin-bottom: 2rem;">Ajouter un nouveau manga</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="list-style: none; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('mangas.store') }}" method="POST" enctype="multipart/form-data" style="background-color: var(--bg-card); padding: 2rem; border-radius: 10px;">
            @csrf
            
            <div class="form-group">
                <label for="image" class="form-label">Image de couverture</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                <small style="color: var(--text-secondary); font-size: 0.85rem;">Format accepté : JPG, PNG, GIF (max 5MB)</small>
            </div>

            <div class="form-group">
                <label for="titre" class="form-label">Titre *</label>
                <input type="text" name="titre" id="titre" class="form-control" value="{{ old('titre') }}" required>
            </div>

            <div class="form-group">
                <label for="auteur" class="form-label">Auteur *</label>
                <input type="text" name="auteur" id="auteur" class="form-control" value="{{ old('auteur') }}" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="nombre_tomes" class="form-label">Nombre de tomes *</label>
                <input type="number" name="nombre_tomes" id="nombre_tomes" class="form-control" value="{{ old('nombre_tomes', 1) }}" min="1" required>
            </div>

            <div class="form-group">
                <label for="statut" class="form-label">Statut *</label>
                <select name="statut" id="statut" class="form-control" required>
                    <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="termine" {{ old('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                    <option value="abandonne" {{ old('statut') == 'abandonne' ? 'selected' : '' }}>Abandonné</option>
                </select>
            </div>

            <div class="form-group">
                <label for="note" class="form-label">Note (sur 10)</label>
                <input type="number" name="note" id="note" class="form-control" value="{{ old('note') }}" min="1" max="10">
            </div>

            <div class="form-group">
                <label for="url_lecture_index" class="form-label">
                    Lien de lecture (optionnel)
                    <span style="color: var(--text-secondary); font-size: 0.85rem; font-weight: normal;">
                        - Page d'accueil du manga
                    </span>
                </label>
                <input type="url" 
                       name="url_lecture_index" 
                       id="url_lecture_index" 
                       class="form-control" 
                       value="{{ old('url_lecture_index') }}"
                       placeholder="https://exemple.com/manga/one-piece">
                <small style="color: var(--text-secondary); font-size: 0.85rem;">
                    Lien vers la page index du manga sur votre site de lecture préféré
                </small>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn-primary" style="flex: 1;">Ajouter le manga</button>
                <a href="{{ route('mangas.my-collection') }}" class="btn-primary" style="flex: 1; text-align: center; background-color: var(--bg-hover);">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection
