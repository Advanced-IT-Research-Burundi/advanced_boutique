<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Stock;
use App\Models\User;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'stock_id' => Stock::factory(),
            'user_id' => User::factory(),
            'expense_type_id' => ExpenseType::factory(),
            'amount' => fake()->randomFloat(0, 0, 9999999999.),
            'description' => fake()->text(),
            'expense_date' => fake()->dateTime(),
            'agency_id' => Agency::factory(),
            'created_by' => User::factory()->create()->created_by,
        ];
    }
}
