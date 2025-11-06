<?php

namespace Database\Factories;

use App\Models\Avis;
use App\Models\Manga;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvisFactory extends Factory
{
    protected $model = Avis::class;

    public function definition(): array
    {
        return [
            'manga_id' => Manga::factory(),
            'user_id' => User::factory(),
            'note' => fake()->numberBetween(1, 10),
            'commentaire' => fake()->paragraph(2),
            'modere' => fake()->boolean(80), // 80% de chance d'être modéré
        ];
    }

    // State pour créer un avis non modéré
    public function nonModere(): static
    {
        return $this->state(fn (array $attributes) => [
            'modere' => false,
        ]);
    }

    // State pour créer un avis modéré
    public function modere(): static
    {
        return $this->state(fn (array $attributes) => [
            'modere' => true,
        ]);
    }

    // State pour créer un avis avec une bonne note
    public function bonneNote(): static
    {
        return $this->state(fn (array $attributes) => [
            'note' => fake()->numberBetween(8, 10),
        ]);
    }

    // State pour créer un avis avec une mauvaise note
    public function mauvaiseNote(): static
    {
        return $this->state(fn (array $attributes) => [
            'note' => fake()->numberBetween(1, 4),
        ]);
    }
}
