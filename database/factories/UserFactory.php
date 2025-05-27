<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Agency;
use App\Models\Company;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'email_verified_at' => fake()->dateTime(),
            'password' => fake()->password(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->text(),
            'date_of_birth' => fake()->date(),
            'gender' => fake()->word(),
            'profile_photo' => fake()->word(),
            'status' => fake()->word(),
            'role' => fake()->word(),
            'permissions' => '{}',
            'last_login_at' => fake()->dateTime(),
            'must_change_password' => fake()->boolean(),
            'two_factor_enabled' => fake()->boolean(),
            'two_factor_secret' => fake()->word(),
            'recovery_codes' => '{}',
            'company_id' => Company::factory(),
            'agency_id' => Agency::factory(),
            'created_by' => User::factory()->create()->created_by,
            'remember_token' => fake()->uuid(),
            'user_id' => User::factory(),
        ];
    }
}
