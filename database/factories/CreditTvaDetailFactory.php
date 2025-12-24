<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\CreditTvaDetail;

class CreditTvaDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CreditTvaDetail::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'credit_tva_id' => fake()->numberBetween(-10000, 10000),
            'type' => fake()->word(),
            'montant' => fake()->randomFloat(0, 0, 9999999999.),
            'sale_id' => fake()->numberBetween(-10000, 10000),
            'description' => fake()->text(),
            'date' => fake()->date(),
        ];
    }
}
