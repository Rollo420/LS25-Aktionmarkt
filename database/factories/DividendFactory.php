<?php

namespace Database\Factories;

use App\Models\GameTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Stock\Stock;
use App\Models\Dividend;

use \Carbon\Carbon;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dividend>
 */
class DividendFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_id' => Stock::factory(), // fallback
            // create/ensure GameTime via service to avoid duplicate or inconsistent GameTime factory usage
            'game_time_id' => (new \App\Services\GameTimeService())->getOrCreate(\Carbon\Carbon::create((int)date('Y'), (int)date('m'), 1))->id,
            'amount_per_share' => $this->faker->randomFloat(2, 0.5, 2.5),
        ];
    }
}
