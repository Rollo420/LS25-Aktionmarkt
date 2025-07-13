<?php

namespace Database\Factories;

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
            'distribution_date' => now(),
            'amount_per_share' => $this->faker->randomFloat(2, 0.5, 2.5),
        ];
    }
}
