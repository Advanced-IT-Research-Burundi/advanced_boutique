<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\User;

class PurchaseItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'purchase_id' => Purchase::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'purchase_price' => fake()->randomFloat(0, 0, 9999999999.),
            'subtotal' => fake()->randomFloat(0, 0, 9999999999.),
            'agency_id' => Agency::factory(),
            'created_by' => User::factory()->create()->created_by,
            'user_id' => User::factory(),
        ];
    }
}
