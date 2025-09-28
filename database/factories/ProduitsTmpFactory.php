<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ProduitsTmp;

class ProduitsTmpFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProduitsTmp::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'code' => fake()->word(),
            'designation' => fake()->word(),
            'PVHT' => fake()->randomFloat(0, 0, 9999999999.),
            'PVTTC' => fake()->randomFloat(0, 0, 9999999999.),
        ];
    }
}
