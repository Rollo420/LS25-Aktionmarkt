<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Credit;
use App\Models\User;
use App\Models\Bank;

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
        $bankIds = Bank::pluck('id')->toArray();
        return [
            'bank_id' => fake()->randomElement($bankIds),
            'name' => fake()->word(),
            'amount' => fake()->randomFloat(2, 0, 10000),
            'interest_rate' => fake()->randomFloat(2, 1, 5),
        ];
    }
}
