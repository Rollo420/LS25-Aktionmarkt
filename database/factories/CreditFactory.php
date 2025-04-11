<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Credit;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Credit>
 */
class CreditFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {


        return [
            'bank_id' => fake()->numberBetween(1, 6), // Assuming you have 5 banks
            'name' => fake()->word(),	
            'amount' => fake()->randomFloat(2, 0, 10000),
            'interest_rate' => fake()->randomFloat(2, 1, 5),
        ];
    }
}
