<?php

namespace Database\Factories;

use App\Models\Manga;
use Illuminate\Database\Eloquent\Factories\Factory;

class TomeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'manga_id' => Manga::factory(),
            'numero' => fake()->numberBetween(1, 50),
            'possede' => fake()->boolean(70),
            'date_achat' => fake()->optional(0.7)->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
