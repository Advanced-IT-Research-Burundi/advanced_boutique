<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\InvoincePointer;

class InvoincePointerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InvoincePointer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'stock_id' => fake()->numberBetween(-10000, 10000),
            'invoince_number' => fake()->numberBetween(-10000, 10000),
            'description' => fake()->text(),
        ];
    }
}
