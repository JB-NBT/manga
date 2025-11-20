@extends('layouts.app')

@section('title', 'Politique de confidentialité - RGPD')

@section('content')
<div class="legal-page">
    <h1>Politique de confidentialité</h1>
    <p class="legal-subtitle">Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés</p>

    <div class="legal-content">
        <div class="legal-section">
            <h2>1. Responsable du traitement</h2>
            <p>
                Le responsable du traitement des données personnelles est :<br>
                <strong>MangaLibrary</strong><br>
                Adresse : 123 Rue des Mangas, 75000 Paris<br>
                Email : contact@mangalibrary.fr<br>
                Numéro de déclaration CNIL : [Numéro fictif - 1234567]
            </p>
        </div>

        <div class="legal-section">
            <h2>2. Données collectées</h2>
            <p>Dans le cadre de l'utilisation de notre service, nous collectons les données suivantes :</p>

            <h3>Données d'identification</h3>
            <ul>
                <li>Nom d'utilisateur</li>
                <li>Adresse email</li>
                <li>Mot de passe (chiffré)</li>
            </ul>

            <h3>Données de contenu</h3>
            <ul>
                <li>Informations sur vos mangas (titres, auteurs, descriptions, notes)</li>
                <li>Avis et commentaires publiés</li>
                <li>Tickets de support</li>
            </ul>

            <h3>Données techniques</h3>
            <ul>
                <li>Adresse IP</li>
                <li>Type de navigateur</li>
                <li>Date et heure de connexion</li>
                <li>Pages consultées</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2>3. Finalités du traitement</h2>
            <p>Vos données sont collectées pour les finalités suivantes :</p>
            <ul>
                <li><strong>Gestion de votre compte</strong> : création, authentification, gestion de vos préférences</li>
                <li><strong>Fourniture du service</strong> : stockage et affichage de votre bibliothèque de mangas</li>
                <li><strong>Communication</strong> : réponse à vos demandes de support, notifications importantes</li>
                <li><strong>Amélioration du service</strong> : statistiques d'utilisation, correction de bugs</li>
                <li><strong>Sécurité</strong> : prévention de la fraude et des accès non autorisés</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2>4. Base légale du traitement</h2>
            <p>Le traitement de vos données repose sur :</p>
            <ul>
                <li><strong>Votre consentement</strong> : lors de la création de compte et l'acceptation des cookies</li>
                <li><strong>L'exécution du contrat</strong> : fourniture du service de gestion de bibliothèque</li>
                <li><strong>Nos intérêts légitimes</strong> : amélioration du service et sécurité</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2>5. Durée de conservation</h2>
            <table class="legal-table">
                <thead>
                    <tr>
                        <th>Type de données</th>
                        <th>Durée de conservation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Données de compte</td>
                        <td>Jusqu'à suppression du compte + 1 an</td>
                    </tr>
                    <tr>
                        <td>Données de contenu (mangas, avis)</td>
                        <td>Jusqu'à suppression du compte</td>
                    </tr>
                    <tr>
                        <td>Logs de connexion</td>
                        <td>12 mois</td>
                    </tr>
                    <tr>
                        <td>Cookies</td>
                        <td>13 mois maximum</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="legal-section">
            <h2>6. Destinataires des données</h2>
            <p>Vos données personnelles peuvent être accessibles à :</p>
            <ul>
                <li><strong>Notre équipe interne</strong> : administrateurs et modérateurs (accès limité)</li>
                <li><strong>Nos sous-traitants techniques</strong> : hébergeur, services de sauvegarde</li>
            </ul>
            <p class="legal-highlight">
                Vos données ne sont jamais vendues à des tiers et ne font l'objet d'aucun transfert en dehors de l'Union Européenne.
            </p>
        </div>

        <div class="legal-section">
            <h2>7. Vos droits (RGPD)</h2>
            <p>Conformément au RGPD, vous disposez des droits suivants :</p>

            <div class="rights-grid">
                <div class="right-card">
                    <h4>Droit d'accès</h4>
                    <p>Obtenir une copie de vos données personnelles</p>
                </div>
                <div class="right-card">
                    <h4>Droit de rectification</h4>
                    <p>Corriger des données inexactes vous concernant</p>
                </div>
                <div class="right-card">
                    <h4>Droit à l'effacement</h4>
                    <p>Demander la suppression de vos données</p>
                </div>
                <div class="right-card">
                    <h4>Droit à la portabilité</h4>
                    <p>Récupérer vos données dans un format lisible</p>
                </div>
                <div class="right-card">
                    <h4>Droit d'opposition</h4>
                    <p>Vous opposer au traitement de vos données</p>
                </div>
                <div class="right-card">
                    <h4>Droit à la limitation</h4>
                    <p>Limiter le traitement de vos données</p>
                </div>
            </div>

            <p>Pour exercer ces droits, contactez-nous à : <strong>rgpd@mangalibrary.fr</strong></p>
        </div>

        <div class="legal-section">
            <h2>8. Gestion des cookies</h2>
            <p>Notre site utilise des cookies pour :</p>
            <ul>
                <li><strong>Cookies essentiels</strong> : nécessaires au fonctionnement du site (session, sécurité)</li>
                <li><strong>Cookies analytiques</strong> : mesure d'audience et amélioration du service</li>
                <li><strong>Cookies de préférences</strong> : mémorisation de vos choix (thème, langue)</li>
            </ul>
            <p>Vous pouvez gérer vos préférences de cookies via le bandeau affiché lors de votre première visite ou en effaçant les cookies de votre navigateur.</p>
        </div>

        <div class="legal-section">
            <h2>9. Sécurité des données</h2>
            <p>Nous mettons en oeuvre des mesures techniques et organisationnelles appropriées :</p>
            <ul>
                <li>Chiffrement des mots de passe (bcrypt)</li>
                <li>Connexion sécurisée HTTPS</li>
                <li>Protection contre les attaques CSRF et XSS</li>
                <li>Sauvegardes régulières</li>
                <li>Accès restreint aux données personnelles</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2>10. Réclamation</h2>
            <p>
                Si vous estimez que le traitement de vos données ne respecte pas la réglementation,
                vous pouvez introduire une réclamation auprès de la CNIL :
            </p>
            <p>
                <strong>Commission Nationale de l'Informatique et des Libertés (CNIL)</strong><br>
                3 Place de Fontenoy - TSA 80715<br>
                75334 PARIS CEDEX 07<br>
                <a href="https://www.cnil.fr" target="_blank">www.cnil.fr</a>
            </p>
        </div>

        <div class="legal-section">
            <p class="legal-update">
                <strong>Dernière mise à jour :</strong> {{ date('d/m/Y') }}
            </p>
        </div>
    </div>
</div>

<style>
.legal-page {
    max-width: 900px;
    margin: 0 auto;
}

.legal-page h1 {
    color: var(--accent);
    margin-bottom: 0.5rem;
}

.legal-subtitle {
    color: var(--text-secondary);
    margin-bottom: 2rem;
    font-size: 1rem;
}

.legal-content {
    background-color: var(--bg-card);
    border-radius: 12px;
    padding: 2rem;
}

.legal-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border);
}

.legal-section:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.legal-section h2 {
    color: var(--accent);
    font-size: 1.3rem;
    margin-bottom: 1rem;
}

.legal-section h3 {
    color: var(--text-primary);
    font-size: 1.1rem;
    margin: 1.5rem 0 0.75rem;
}

.legal-section p {
    color: var(--text-secondary);
    line-height: 1.7;
    margin-bottom: 1rem;
}

.legal-section ul {
    color: var(--text-secondary);
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.legal-section li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.legal-highlight {
    background-color: rgba(6, 214, 160, 0.1);
    border-left: 4px solid var(--success);
    padding: 1rem;
    border-radius: 0 8px 8px 0;
}

.legal-table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.legal-table th,
.legal-table td {
    padding: 0.75rem 1rem;
    text-align: left;
    border: 1px solid var(--border);
}

.legal-table th {
    background-color: var(--bg-hover);
    color: var(--text-primary);
    font-weight: 600;
}

.legal-table td {
    color: var(--text-secondary);
}

.rights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 1.5rem 0;
}

.right-card {
    background-color: var(--bg-hover);
    padding: 1rem;
    border-radius: 8px;
    border-left: 3px solid var(--accent);
}

.right-card h4 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.right-card p {
    color: var(--text-secondary);
    font-size: 0.85rem;
    margin: 0;
}

.legal-update {
    text-align: center;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.legal-section a {
    color: var(--accent);
    text-decoration: none;
}

.legal-section a:hover {
    text-decoration: underline;
}
</style>
@endsection
