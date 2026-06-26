<?php

namespace Database\Factories;

use App\Models\Annonce;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Annonce>
 */
class AnnonceFactory extends Factory
{
    protected $model = Annonce::class;

    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(4),
            'niveau' => fake()->randomElement(Annonce::NIVEAUX),
            'contenu' => fake()->paragraphs(2, true),
            'published_at' => now()->subHour(),
            'expires_at' => null,
            'created_by' => User::factory(),
        ];
    }

    public function brouillon(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
