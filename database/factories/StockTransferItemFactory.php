<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\User;

class StockTransferItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockTransferItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'stock_transfer_id' => StockTransfer::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'agency_id' => Agency::factory(),
            'created_by' => User::factory()->create()->created_by,
            'user_id' => User::factory(),
        ];
    }
}
