<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MangaFactory extends Factory
{
    public function definition(): array
    {
        $mangas = [
            ['titre' => 'One Piece', 'auteur' => 'Eiichiro Oda'],
            ['titre' => 'Naruto', 'auteur' => 'Masashi Kishimoto'],
            ['titre' => 'Death Note', 'auteur' => 'Tsugumi Ohba'],
            ['titre' => 'Attack on Titan', 'auteur' => 'Hajime Isayama'],
            ['titre' => 'My Hero Academia', 'auteur' => 'Kohei Horikoshi'],
            ['titre' => 'Demon Slayer', 'auteur' => 'Koyoharu Gotouge'],
            ['titre' => 'Tokyo Ghoul', 'auteur' => 'Sui Ishida'],
            ['titre' => 'Hunter x Hunter', 'auteur' => 'Yoshihiro Togashi'],
            ['titre' => 'Fullmetal Alchemist', 'auteur' => 'Hiromu Arakawa'],
            ['titre' => 'Bleach', 'auteur' => 'Tite Kubo'],
            ['titre' => 'Dragon Ball', 'auteur' => 'Akira Toriyama'],
            ['titre' => 'Berserk', 'auteur' => 'Kentaro Miura'],
            ['titre' => 'Vinland Saga', 'auteur' => 'Makoto Yukimura'],
            ['titre' => 'Jujutsu Kaisen', 'auteur' => 'Gege Akutami'],
            ['titre' => 'Chainsaw Man', 'auteur' => 'Tatsuki Fujimoto'],
        ];

        $manga = fake()->randomElement($mangas);

        return [
            'user_id' => User::factory(),
            'titre' => $manga['titre'],
            'auteur' => $manga['auteur'],
            'description' => fake()->paragraph(3),
            'image_couverture' => null,
            'nombre_tomes' => fake()->numberBetween(1, 100),
            'statut' => fake()->randomElement(['en_cours', 'termine', 'abandonne']),
            'note' => fake()->optional(0.7)->numberBetween(1, 10),
        ];
    }
}
