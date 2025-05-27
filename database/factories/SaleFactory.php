<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;

class SaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'stock_id' => Stock::factory(),
            'user_id' => User::factory(),
            'total_amount' => fake()->randomFloat(0, 0, 9999999999.),
            'paid_amount' => fake()->randomFloat(0, 0, 9999999999.),
            'due_amount' => fake()->randomFloat(0, 0, 9999999999.),
            'sale_date' => fake()->dateTime(),
            'created_by' => User::factory()->create()->id,
        ];
    }
}
