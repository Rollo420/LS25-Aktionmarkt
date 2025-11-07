<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\GameTime;
use App\Models\Stock\Stock;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BuyTransaction>
 */
class BuyTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stock = Stock::inRandomOrder()->first() ?? Stock::factory()->create();

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id,
            // Always use the latest GameTime for synchronization with Price
            'game_time_id' => \App\Models\GameTime::latest()->first()?->id ?? \App\Models\GameTime::factory()->create()->id,
            'stock_id' => $stock->id,
            'quantity' => fake()->numberBetween(1, 100),
            'status' => fake()->boolean(),
            'type' => 'buy',
            'price_at_buy' => $stock->getCurrentPrice(),
        ];
    }
}
