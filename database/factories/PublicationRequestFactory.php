<?php

namespace Database\Factories;

use App\Models\PublicationRequest;
use App\Models\Manga;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PublicationRequestFactory extends Factory
{
    protected $model = PublicationRequest::class;

    public function definition(): array
    {
        return [
            'manga_id' => Manga::factory(),
            'user_id' => User::factory(),
            'statut' => fake()->randomElement(['en_attente', 'approuve', 'refuse']),
            'message_admin' => fake()->optional(0.3)->sentence(),
        ];
    }

    // State pour créer une demande en attente
    public function enAttente(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_attente',
            'message_admin' => null,
        ]);
    }

    // State pour créer une demande approuvée
    public function approuve(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'approuve',
            'message_admin' => fake()->optional(0.5)->sentence(),
        ]);
    }

    // State pour créer une demande refusée
    public function refuse(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'refuse',
            'message_admin' => fake()->sentence(),
        ]);
    }
}
