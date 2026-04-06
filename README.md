# MangaLibrary

Application web de gestion de collections de mangas, développée dans le cadre du BTS SIO option SLAM (épreuve E6).

- **Production** : http://192.168.56.111
- **GitHub** : https://github.com/JB-NBT/manga/tree/main
- **Documentation API** : http://192.168.56.111/docs/api

---

## Prérequis

| Outil | Version minimale |
|-------|-----------------|
| PHP | 8.2+ |
| Composer | 2.x |
| Node.js | 18+ |
| NPM | 9+ |
| MySQL | 8.0+ |
| Apache | 2.4+ |

---

## Installation automatique

Cloner le dépôt puis lancer le script d'installation :

```bash
git clone https://github.com/JB-NBT/manga.git
cd manga
bash install.sh
```

Le script effectue automatiquement :
- Installation des dépendances PHP (Composer) et JS (NPM)
- Configuration du fichier `.env`
- Génération de la clé d'application
- Compilation des assets (Vite)
- Migrations et seeders (rôles, utilisateurs, mangas avec couvertures)
- Création du lien symbolique `storage`

---

## Installation manuelle

Si vous préférez installer étape par étape :

**1. Cloner le dépôt**
```bash
git clone https://github.com/JB-NBT/manga.git
cd manga
```

**2. Installer les dépendances**
```bash
composer install
npm install && npm run build
```

**3. Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

Éditer `.env` avec vos paramètres de base de données :
```env
DB_DATABASE=manga_library
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
APP_URL=http://localhost
```

**4. Initialiser la base de données**
```bash
php artisan migrate:fresh --seed
```

**5. Lier le stockage**
```bash
php artisan storage:link
```

**6. Lancer le serveur de développement**
```bash
php artisan serve
```

L'application est accessible sur http://localhost:8000

---

## Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Administrateur | admin@manga.local | password123 |
| Modérateur | moderator@manga.local | password123 |
| Utilisateur 1 | user@manga.local | password123 |
| Utilisateur 2 | user2@manga.local | password123 |

---

## Accès administration (compte admin)

| Outil | URL |
|-------|-----|
| phpMyAdmin | http://192.168.56.111/phpmyadmin |
| GLPI | http://192.168.56.111:8080 |
| Documentation API | http://192.168.56.111/docs/api |

> Ces outils sont accessibles via les boutons dans le footer, réservés aux administrateurs.

---

## Stack technique

- **Framework** : Laravel 12 (PHP 8.2)
- **Base de données** : MySQL 8.0 + Eloquent ORM
- **Authentification** : Laravel UI + Spatie Laravel-Permission
- **Frontend** : Blade + Bootstrap 5 + Vite
- **Tests** : PHPUnit 11 (32 tests Feature)
- **Serveur** : Apache 2.4 (Ubuntu Server 24.04)

---

## Lancer les tests

```bash
php artisan test
```

---

## Structure du projet

```
app/
├── Console/Commands/       # Commandes Artisan (ex: rejet auto demandes)
├── Http/Controllers/       # Controllers (Manga, Avis, Ticket, Publication...)
├── Models/                 # Modèles Eloquent
└── Policies/               # Policies d'autorisation

database/
├── migrations/             # Structure de la base de données
├── seeders/                # Données de test (users, mangas, rôles)
└── seeders/images/         # Couvertures manga pour le seed

resources/views/            # Templates Blade
routes/web.php              # Définition des routes
tests/Feature/              # Tests PHPUnit
docs/uml/                   # Diagrammes UML (PlantUML)
public/docs/api/            # Documentation phpDocumentor
```
