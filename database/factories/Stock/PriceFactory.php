<?php

namespace Database\Factories\Stock;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Stock\Stock;
use App\Models\GameTime;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock\Price>
 */
class PriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_id' => Stock::factory(),
            'game_time_id' => GameTime::factory(),
            'name' => fake()->randomFloat(2, 10, 500),
        ];
    }
}
