<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Commandes;

class CommandesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Commandes::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'vehicule_id' => fake()->numberBetween(-10000, 10000),
            'matricule' => fake()->word(),
            'commentaire' => fake()->text(),
            'poids' => fake()->randomFloat(0, 0, 9999999999.),
            'date_livraison' => fake()->date(),
            'description' => fake()->text(),
        ];
    }
}
