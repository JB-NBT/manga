@extends('layouts.app')

@section('title', 'Modifier ' . $manga->titre)

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="color: var(--accent); margin-bottom: 2rem;">Modifier "{{ $manga->titre }}"</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="list-style: none; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('mangas.update', $manga) }}" method="POST" enctype="multipart/form-data" style="background-color: var(--bg-card); padding: 2rem; border-radius: 10px;">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="image" class="form-label">Changer l'image de couverture</label>
                @if($manga->image_couverture)
                    <div style="margin-bottom: 1rem;">
                        <img src="{{ asset('storage/' . $manga->image_couverture) }}" alt="Couverture actuelle" style="max-width: 200px; border-radius: 5px;">
                    </div>
                @endif
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                <small style="color: var(--text-secondary); font-size: 0.85rem;">Laisser vide pour conserver l'image actuelle</small>
            </div>

            <div class="form-group">
                <label for="titre" class="form-label">Titre *</label>
                <input type="text" name="titre" id="titre" class="form-control" value="{{ old('titre', $manga->titre) }}" required>
            </div>

            <div class="form-group">
                <label for="auteur" class="form-label">Auteur *</label>
                <input type="text" name="auteur" id="auteur" class="form-control" value="{{ old('auteur', $manga->auteur) }}" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $manga->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="nombre_tomes" class="form-label">Nombre de tomes *</label>
                <input type="number" name="nombre_tomes" id="nombre_tomes" class="form-control" value="{{ old('nombre_tomes', $manga->nombre_tomes) }}" min="1" required>
            </div>

            <div class="form-group">
                <label for="statut" class="form-label">Statut *</label>
                <select name="statut" id="statut" class="form-control" required>
                    <option value="en_cours" {{ old('statut', $manga->statut) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="termine" {{ old('statut', $manga->statut) == 'termine' ? 'selected' : '' }}>Terminé</option>
                    <option value="abandonne" {{ old('statut', $manga->statut) == 'abandonne' ? 'selected' : '' }}>Abandonné</option>
                </select>
            </div>

            <div class="form-group">
                <label for="note" class="form-label">Note (sur 10)</label>
                <input type="number" name="note" id="note" class="form-control" value="{{ old('note', $manga->note) }}" min="1" max="10">
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn-primary" style="flex: 1;">Enregistrer les modifications</button>
                <a href="{{ route('mangas.show', $manga) }}" class="btn-primary" style="flex: 1; text-align: center; background-color: var(--bg-hover);">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection
