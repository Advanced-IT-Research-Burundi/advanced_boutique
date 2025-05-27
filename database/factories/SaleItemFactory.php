<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;

class SaleItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SaleItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'sale_price' => fake()->randomFloat(0, 0, 9999999999.),
            'discount' => fake()->randomFloat(0, 0, 9999999999.),
            'subtotal' => fake()->randomFloat(0, 0, 9999999999.),
            'created_by' => User::factory()->create()->id,
        ];
    }
}
