@extends('layouts.app')

@section('title', 'Politique de confidentialité')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="color: var(--accent); margin-bottom: 2rem;">Politique de confidentialité</h1>
        
        <div style="background-color: var(--bg-card); padding: 2rem; border-radius: 10px; line-height: 1.6;">
            <h2 style="color: var(--accent); margin-top: 0;">Collecte des données</h2>
            <p>
                Nous collectons les informations suivantes :
            </p>
            <ul>
                <li>Nom et prénom</li>
                <li>Adresse email</li>
                <li>Informations sur vos mangas (titres, auteurs, notes)</li>
            </ul>

            <h2 style="color: var(--accent);">Utilisation des données</h2>
            <p>
                Vos données sont utilisées uniquement pour le fonctionnement du site et ne sont jamais 
                vendues à des tiers.
            </p>

            <h2 style="color: var(--accent);">Sécurité</h2>
            <p>
                Nous mettons en œuvre des mesures de sécurité appropriées pour protéger vos données 
                contre tout accès non autorisé.
            </p>

            <h2 style="color: var(--accent);">Vos droits</h2>
            <p>
                Conformément au RGPD, vous disposez des droits suivants :
            </p>
            <ul>
                <li>Droit d'accès à vos données</li>
                <li>Droit de rectification</li>
                <li>Droit à l'effacement</li>
                <li>Droit à la portabilité</li>
            </ul>
        </div>
    </div>
@endsection
