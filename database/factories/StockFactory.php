<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Stock;
use App\Models\User;

class StockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stock::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'location' => fake()->word(),
            'description' => fake()->text(),
            'agency_id' => Agency::factory(),
            'created_by' => User::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
        ];
    }
}
