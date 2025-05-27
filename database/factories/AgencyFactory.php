<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Company;
use App\Models\User;

class AgencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Agency::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'code' => fake()->word(),
            'name' => fake()->name(),
            'adresse' => fake()->word(),
            'manager_id' => User::factory(),
            'parent_agency_id' => Agency::factory(),
            'is_main_office' => fake()->boolean(),
            'created_by' => User::factory()->create()->created_by,
            'user_id' => User::factory(),
            'agency_id' => Agency::factory(),
        ];
    }
}
