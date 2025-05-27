<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Payment;
use App\Models\User;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'payment_type' => fake()->word(),
            'reference_id' => fake()->numberBetween(-10000, 10000),
            'amount' => fake()->randomFloat(0, 0, 9999999999.),
            'payment_method' => fake()->word(),
            'payment_date' => fake()->dateTime(),
            'created_by' => User::factory()->create()->id,
        ];
    }
}
