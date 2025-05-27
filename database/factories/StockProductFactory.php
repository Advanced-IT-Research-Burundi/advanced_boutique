<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockProduct;

class StockProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockProduct::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'stock_id' => Stock::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->randomFloat(0, 0, 9999999999.),
            'agency_id' => Agency::factory(),
        ];
    }
}
