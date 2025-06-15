<?php

namespace Database\Factories\Stock;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Stock\Stock;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public $stock_id = 1;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? 1,
            'stock_id' => Stock::inRandomOrder()->first()?->id ?? 1,
            'status' => fake()->randomElement(['open', 'closed', 'pending', 'completed', 'failed', 'cancelled' ]),
            'quantity' => fake()->numberBetween(1, 100),
                                        // kaufen, verkaufen, einzahlen, abheben, überweisen
            'type' => fake()->randomElement(['buy', 'sell', 'deposit', 'withdraw', 'transfer']),
        ];
    }
}
