@extends('layouts.app')

@section('title', 'Conditions Générales de Vente')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="color: var(--accent); margin-bottom: 2rem;">Conditions Générales de Vente</h1>
        
        <div style="background-color: var(--bg-card); padding: 2rem; border-radius: 10px; line-height: 1.6;">
            <p style="font-style: italic; color: var(--text-secondary);">
                Ce site est un service gratuit de gestion de collection de mangas.
            </p>

            <h2 style="color: var(--accent); margin-top: 2rem;">Article 1 - Objet</h2>
            <p>
                MangaCollection permet aux utilisateurs de gérer leur collection personnelle de mangas.
            </p>

            <h2 style="color: var(--accent);">Article 2 - Inscription</h2>
            <p>
                L'inscription est gratuite et nécessite une adresse email valide.
            </p>

            <h2 style="color: var(--accent);">Article 3 - Utilisation du service</h2>
            <p>
                L'utilisateur s'engage à utiliser le service de manière responsable et conforme aux lois en vigueur.
            </p>
        </div>
    </div>
@endsection
