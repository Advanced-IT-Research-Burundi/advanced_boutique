<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Vehicule;
use App\Models\VehiculeDepense;

class VehiculeDepenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VehiculeDepense::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'vehicule_id' => Vehicule::factory(),
            'amount' => fake()->randomFloat(0, 0, 9999999999.),
            'date' => fake()->dateTime(),
            'description' => fake()->text(),
            'user_id' => User::factory(),
        ];
    }
}
