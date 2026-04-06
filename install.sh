#!/bin/bash

# ============================================================
# Script d'installation automatique - MangaLibrary
# ============================================================
# Usage : bash install.sh
# Prérequis : PHP 8.2, Composer, Node.js, NPM, MySQL
# ============================================================

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

info()    { echo -e "${GREEN}[INFO]${NC} $1"; }
warning() { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

echo ""
echo "============================================"
echo "   Installation de MangaLibrary"
echo "============================================"
echo ""

# Vérification des prérequis
info "Vérification des prérequis..."
command -v php    >/dev/null 2>&1 || error "PHP non trouvé. Installez PHP 8.2+"
command -v composer >/dev/null 2>&1 || error "Composer non trouvé."
command -v node   >/dev/null 2>&1 || error "Node.js non trouvé."
command -v npm    >/dev/null 2>&1 || error "NPM non trouvé."
command -v mysql  >/dev/null 2>&1 || error "MySQL non trouvé."
info "Prérequis OK."

# Configuration de l'environnement
if [ ! -f ".env" ]; then
    info "Création du fichier .env..."
    cp .env.example .env
else
    warning ".env déjà existant, on le conserve."
fi

# Saisie des paramètres
echo ""
echo "--- Configuration ---"
read -p "Nom de la base de données [manga_library] : " DB_NAME
DB_NAME=${DB_NAME:-manga_library}

read -p "URL de l'application [http://localhost] : " APP_URL
APP_URL=${APP_URL:-http://localhost}

# Création du user MySQL dédié via sudo mysql (socket auth)
DB_USER="manga_user"
DB_PASS="MangaLibrary2024!"

info "Création de la base de données et de l'utilisateur MySQL..."
sudo mysql <<EOF
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
EOF

# Mise à jour du .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" .env
sed -i "s|APP_URL=.*|APP_URL=${APP_URL}|" .env

info "Fichier .env configuré (user MySQL : ${DB_USER})"

# Installation des dépendances PHP
info "Installation des dépendances PHP (Composer)..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Génération de la clé d'application
info "Génération de la clé Laravel..."
php artisan key:generate --force

# Installation des dépendances JS et compilation des assets
info "Installation des dépendances NPM..."
npm install

info "Compilation des assets (Vite)..."
npm run build

# Migrations et seeders
info "Exécution des migrations..."
php artisan migrate:fresh --force

info "Exécution des seeders (rôles, utilisateurs, mangas)..."
php artisan db:seed --force

# Lien symbolique storage
info "Création du lien symbolique storage..."
php artisan storage:link

# Permissions sur les dossiers
info "Application des permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo ""
echo "============================================"
echo -e "${GREEN}   Installation terminée avec succès !${NC}"
echo "============================================"
echo ""
echo "  URL de l'application : ${APP_URL}"
echo ""
echo "  Comptes de test :"
echo "    Admin      : admin@manga.local / password123"
echo "    Modérateur : moderator@manga.local / password123"
echo "    User 1     : user@manga.local / password123"
echo "    User 2     : user2@manga.local / password123"
echo ""
echo "  Pour lancer le serveur de développement :"
echo "    php artisan serve"
echo ""
