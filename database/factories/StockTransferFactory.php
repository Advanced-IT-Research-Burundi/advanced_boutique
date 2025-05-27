<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\User;

class StockTransferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockTransfer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'from_stock_id' => Stock::factory(),
            'to_stock_id' => Stock::factory(),
            'user_id' => User::factory(),
            'transfer_date' => fake()->dateTime(),
            'note' => fake()->text(),
            'created_by' => User::factory()->create()->id,
        ];
    }
}
