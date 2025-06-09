<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\User;
use App\Models\Vehicule;

class VehiculeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vehicule::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'model' => fake()->word(),
            'brand' => fake()->word(),
            'year' => fake()->numberBetween(-10000, 10000),
            'color' => fake()->word(),
            'price' => fake()->randomFloat(0, 0, 9999999999.),
            'status' => fake()->word(),
            'description' => fake()->text(),
            'agency_id' => Agency::factory(),
            'created_by' => User::factory()->create()->created_by,
            'user_id' => User::factory(),
        ];
    }
}
