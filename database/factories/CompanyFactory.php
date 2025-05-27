<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\User;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tp_name' => fake()->word(),
            'tp_type' => fake()->word(),
            'tp_TIN' => fake()->word(),
            'tp_trade_number' => fake()->word(),
            'tp_postal_number' => fake()->word(),
            'tp_phone_number' => fake()->word(),
            'tp_address_province' => fake()->word(),
            'tp_address_commune' => fake()->word(),
            'tp_address_quartier' => fake()->word(),
            'tp_address_avenue' => fake()->word(),
            'tp_address_rue' => fake()->word(),
            'tp_address_number' => fake()->word(),
            'vat_taxpayer' => fake()->word(),
            'ct_taxpayer' => fake()->word(),
            'tl_taxpayer' => fake()->word(),
            'tp_fiscal_center' => fake()->word(),
            'tp_activity_sector' => fake()->word(),
            'tp_legal_form' => fake()->word(),
            'payment_type' => fake()->word(),
            'is_actif' => fake()->boolean(),
            'user_id' => User::factory(),
        ];
    }
}
