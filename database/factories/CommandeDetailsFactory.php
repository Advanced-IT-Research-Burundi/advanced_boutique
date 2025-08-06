<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\CommandeDetails;

class CommandeDetailsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CommandeDetails::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'commande_id' => fake()->numberBetween(-10000, 10000),
            'produit_id' => fake()->word(),
            'produit_code' => fake()->word(),
            'produit_name' => fake()->word(),
            'company_code' => fake()->numberBetween(-10000, 10000),
            'quantite' => fake()->randomFloat(0, 0, 9999999999.),
            'poids' => fake()->randomFloat(0, 0, 9999999999.),
            'prix_unitaire' => fake()->randomFloat(0, 0, 9999999999.),
            'remise' => fake()->randomFloat(0, 0, 9999999999.),
            'date_livraison' => fake()->date(),
            'statut' => fake()->word(),
        ];
    }
}
