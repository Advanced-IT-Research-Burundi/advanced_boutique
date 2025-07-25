<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Proforma;
use App\Models\Stock;
use App\Models\User;

class ProformaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Proforma::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'stock_id' => Stock::factory(),
            'user_id' => User::factory(),
            'total_amount' => fake()->randomFloat(0, 0, 9999999999.),
            'due_amount' => fake()->randomFloat(0, 0, 9999999999.),
            'sale_date' => fake()->date(),
            'note' => fake()->text(),
            'invoice_type' => fake()->word(),
            'agency_id' => Agency::factory(),
            'created_by' => User::factory()->create()->created_by,
            'proforma_items' => fake()->text(),
            'client' => fake()->text(),
        ];
    }
}
