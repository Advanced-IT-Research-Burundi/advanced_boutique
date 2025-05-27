<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'category_id' => Category::factory(),
            'purchase_price' => fake()->randomFloat(0, 0, 9999999999.),
            'sale_price' => fake()->randomFloat(0, 0, 9999999999.),
            'unit' => fake()->word(),
            'alert_quantity' => fake()->randomFloat(0, 0, 9999999999.),
            'created_by' => User::factory()->create()->id,
        ];
    }
}
