<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'nom' => fake()->lastName().' '.fake()->firstName(),
            'matricule' => fake()->unique()->numerify('M####'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => User::ROLE_AGENT,
            'bureau_id' => null,
            'service_id' => null,
            'photo_reference' => null,
        ];
    }

    public function administrateur(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function coordinateur(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_COORDINATEUR,
        ]);
    }

    public function chefBureau(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_CHEF_BUREAU,
        ]);
    }

    public function agent(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_AGENT,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
