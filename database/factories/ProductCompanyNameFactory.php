<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ProductCompanyName;

class ProductCompanyNameFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductCompanyName::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_code' => fake()->word(),
            'company_code' => fake()->word(),
            'item_name' => fake()->word(),
            'size' => fake()->word(),
            'packing_details' => fake()->word(),
            'mfg_location' => fake()->word(),
            'weight_kg' => fake()->randomFloat(0, 0, 9999999999.),
            'order_qty' => fake()->randomFloat(0, 0, 9999999999.),
            'total_weight' => fake()->randomFloat(0, 0, 9999999999.),
            'pu' => fake()->word(),
            'total_weight_pu' => fake()->randomFloat(0, 0, 9999999999.),
        ];
    }
}
