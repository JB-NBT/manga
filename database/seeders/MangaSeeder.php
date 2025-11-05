<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Manga;
use App\Models\User;

class MangaSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les users de test
        $user1 = User::where('email', 'user@manga.local')->first();
        $user2 = User::where('email', 'user2@manga.local')->first();

        if (!$user1 || !$user2) {
            echo "⚠️ Users de test non trouvés. Lance RolePermissionSeeder d'abord.\n";
            return;
        }

        // ========================================
        // MANGAS PRIVÉS DE USER 1
        // ========================================
        
        $mangasUser1 = [
            [
                'titre' => 'One Piece',
                'auteur' => 'Eiichiro Oda',
                'description' => 'L\'histoire de Monkey D. Luffy qui rêve de devenir le Roi des Pirates.',
                'nombre_tomes' => 106,
                'statut' => 'en_cours',
                'note' => 10,
                'est_public' => false,
            ],
            [
                'titre' => 'Naruto',
                'auteur' => 'Masashi Kishimoto',
                'description' => 'Les aventures de Naruto Uzumaki, ninja rêvant de devenir Hokage.',
                'nombre_tomes' => 72,
                'statut' => 'termine',
                'note' => 9,
                'est_public' => false,
            ],
            [
                'titre' => 'Death Note',
                'auteur' => 'Tsugumi Ohba',
                'description' => 'Light Yagami trouve un cahier permettant de tuer quiconque dont le nom est écrit dedans.',
                'nombre_tomes' => 12,
                'statut' => 'termine',
                'note' => 10,
                'est_public' => false,
            ],
        ];

        foreach ($mangasUser1 as $mangaData) {
            Manga::create(array_merge($mangaData, ['user_id' => $user1->id]));
        }

        // ========================================
        // MANGAS PRIVÉS DE USER 2
        // ========================================
        
        $mangasUser2 = [
            [
                'titre' => 'Attack on Titan',
                'auteur' => 'Hajime Isayama',
                'description' => 'L\'humanité vit retranchée face aux Titans géants mangeurs d\'hommes.',
                'nombre_tomes' => 34,
                'statut' => 'termine',
                'note' => 9,
                'est_public' => false,
            ],
            [
                'titre' => 'My Hero Academia',
                'auteur' => 'Kohei Horikoshi',
                'description' => 'Dans un monde où 80% des gens ont des super-pouvoirs, Izuku Midoriya rêve de devenir un héros.',
                'nombre_tomes' => 38,
                'statut' => 'en_cours',
                'note' => 8,
                'est_public' => false,
            ],
        ];

        foreach ($mangasUser2 as $mangaData) {
            Manga::create(array_merge($mangaData, ['user_id' => $user2->id]));
        }

        // ========================================
        // MANGAS PUBLICS (déjà approuvés)
        // ========================================
        
        $mangasPublics = [
            [
                'user_id' => $user1->id,
                'titre' => 'Demon Slayer',
                'auteur' => 'Koyoharu Gotouge',
                'description' => 'Tanjiro devient un chasseur de démons pour sauver sa sœur transformée.',
                'nombre_tomes' => 23,
                'statut' => 'termine',
                'note' => 9,
                'est_public' => true,
                'note_moyenne' => 8.5,
                'nombre_avis' => 12,
            ],
            [
                'user_id' => $user2->id,
                'titre' => 'Fullmetal Alchemist',
                'auteur' => 'Hiromu Arakawa',
                'description' => 'Deux frères alchimistes cherchent la Pierre Philosophale pour retrouver leurs corps.',
                'nombre_tomes' => 27,
                'statut' => 'termine',
                'note' => 10,
                'est_public' => true,
                'note_moyenne' => 9.2,
                'nombre_avis' => 25,
            ],
            [
                'user_id' => $user1->id,
                'titre' => 'Tokyo Ghoul',
                'auteur' => 'Sui Ishida',
                'description' => 'Ken Kaneki devient un mi-humain mi-goule après une greffe d\'organes.',
                'nombre_tomes' => 14,
                'statut' => 'termine',
                'note' => 8,
                'est_public' => true,
                'note_moyenne' => 7.8,
                'nombre_avis' => 18,
            ],
        ];

        foreach ($mangasPublics as $mangaData) {
            Manga::create($mangaData);
        }

        echo "\n✅ Mangas créés :\n";
        echo "   - 3 mangas privés pour user@manga.local\n";
        echo "   - 2 mangas privés pour user2@manga.local\n";
        echo "   - 3 mangas publics (bibliothèque)\n\n";
    }
}
