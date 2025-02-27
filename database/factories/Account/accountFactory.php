<?php

namespace Database\Factories\Account;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account\account>
 */
class accountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            //'password_id' => fake()->unique()->numberBetween(1, 5),
            'mail' => fake()->safeEmail(),
            'is_verified' => fake()->boolean(),

        ];
    }
}
