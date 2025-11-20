@extends('layouts.app')

@section('title', 'Mentions légales')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="color: var(--accent); margin-bottom: 2rem;">Mentions légales</h1>
        
        <div style="background-color: var(--bg-card); padding: 2rem; border-radius: 10px; line-height: 1.6;">
            <h2 style="color: var(--accent); margin-top: 0;">Éditeur du site</h2>
            <p>
                <strong>Nom du site :</strong> MangaCollection<br>
                <strong>Propriétaire :</strong> [Ton nom ou ta société]<br>
                <strong>Adresse :</strong> [Ton adresse]<br>
                <strong>Email :</strong> [Ton email]<br>
                <strong>Téléphone :</strong> [Ton téléphone]
            </p>

            <h2 style="color: var(--accent);">Hébergeur</h2>
            <p>
                <strong>Nom :</strong> [Nom de ton hébergeur]<br>
                <strong>Adresse :</strong> [Adresse de l'hébergeur]<br>
                <strong>Téléphone :</strong> [Téléphone de l'hébergeur]
            </p>

            <h2 style="color: var(--accent);">Propriété intellectuelle</h2>
            <p>
                L'ensemble du contenu de ce site (textes, images, vidéos) est protégé par le droit d'auteur. 
                Toute reproduction, même partielle, est interdite sans autorisation préalable.
            </p>

            <h2 style="color: var(--accent);">Données personnelles</h2>
            <p>
                Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression 
                de vos données personnelles. Pour exercer ce droit, contactez-nous à [ton email].
            </p>
        </div>
    </div>
@endsection
