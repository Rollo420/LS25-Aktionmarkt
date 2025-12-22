<?php

namespace Database\Factories\Stock;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\GameTime;
use App\Models\Stock\Stock;

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
        $gt = new GameTime();
        return [
            'stock_id' => Stock::factory(),
            'game_time_id' => $gt->getCurrentGameTime()->id,

            'name' => fake()->randomFloat(2, 30000, 800000),
        ];
    }
}
