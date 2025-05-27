<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\CashRegister;
use App\Models\Stock;
use App\Models\User;

class CashRegisterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CashRegister::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'stock_id' => Stock::factory(),
            'opening_balance' => fake()->randomFloat(0, 0, 9999999999.),
            'closing_balance' => fake()->randomFloat(0, 0, 9999999999.),
            'status' => fake()->word(),
            'opened_at' => fake()->dateTime(),
            'closed_at' => fake()->dateTime(),
            'created_by' => User::factory()->create()->id,
        ];
    }
}
