<?php

namespace Database\Factories\Stock;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            'user_id' => fake()->numberBetween(1, 5),
            'stock_id' => fake()->numberBetween(1,3), //$this->stock_id++,
            'status' => fake()->boolean(),
            'quantity' => fake()->numberBetween(1, 100),            
        ];
    }
}
