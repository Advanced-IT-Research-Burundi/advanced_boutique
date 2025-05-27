<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\CashRegister;
use App\Models\CashTransaction;
use App\Models\User;

class CashTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CashTransaction::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'cash_register_id' => CashRegister::factory(),
            'type' => fake()->word(),
            'reference_id' => fake()->numberBetween(-10000, 10000),
            'amount' => fake()->randomFloat(0, 0, 9999999999.),
            'description' => fake()->text(),
            'agency_id' => Agency::factory(),
            'created_by' => User::factory()->create()->created_by,
            'user_id' => User::factory(),
        ];
    }
}
