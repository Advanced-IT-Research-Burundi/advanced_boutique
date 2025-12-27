<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AutreElement;

class AutreElementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AutreElement::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'date' => fake()->date(),
            'libelle' => fake()->word(),
            'emplacement' => fake()->word(),
            'quantite' => fake()->randomFloat(2, 0, 99999999.99),
            'valeur' => fake()->randomFloat(2, 0, 9999999999999.99),
            'devise' => fake()->regexify('[A-Za-z0-9]{10}'),
            'type_element' => fake()->randomElement(["caisse","banque","avance","credit","investissement","immobilisation","autre"]),
            'reference' => fake()->regexify('[A-Za-z0-9]{100}'),
            'observation' => fake()->text(),
            'document' => fake()->word(),
        ];
    }
}
