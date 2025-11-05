<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "\n🚀 DÉMARRAGE DU SEEDING\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        // 1. Créer les rôles, permissions et users de test
        echo "📝 Étape 1/2 : Création des rôles et users...\n";
        $this->call(RolePermissionSeeder::class);

        // 2. Créer les mangas de test
        echo "📚 Étape 2/2 : Création des mangas...\n";
        $this->call(MangaSeeder::class);

        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✅ SEEDING TERMINÉ AVEC SUCCÈS !\n\n";
        echo "🌐 Accède à ton application :\n";
        echo "   → http://127.0.0.1:8000\n\n";
    }
}
