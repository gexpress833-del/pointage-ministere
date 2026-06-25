<?php

namespace Database\Factories;

use App\Models\Bureau;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bureau>
 */
class BureauFactory extends Factory
{
    protected $model = Bureau::class;

    public function definition(): array
    {
        return [
            'nom_bureau' => fake()->company(),
            'chef_bureau_id' => null,
        ];
    }
}
