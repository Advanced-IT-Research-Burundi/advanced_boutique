<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\DepenseImportationType;
use App\Models\DepensesImportation;
use App\Models\User;

class DepensesImportationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DepensesImportation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'depense_importation_type' => fake()->word(),
            'depense_importation_type_id' => DepenseImportationType::factory(),
            'currency' => fake()->word(),
            'exchange_rate' => fake()->randomFloat(0, 0, 9999999999.),
            'amount' => fake()->randomFloat(0, 0, 9999999999.),
            'amount_currency' => fake()->randomFloat(0, 0, 9999999999.),
            'date' => fake()->dateTime(),
            'description' => fake()->text(),
            'user_id' => User::factory(),
        ];
    }
}
