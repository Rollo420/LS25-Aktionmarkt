<?php

namespace Database\Factories;

use App\Models\Month;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameTime>
 */
class GameTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'month_id' => Month::inRandomOrder()->first()?->id ?? \App\Models\Month::factory()->create()->id,
            // realistic in-game years (e.g. 2000-2050) to avoid tiny year numbers like 14 in charts
            'current_year' => fake()->numberBetween(2000, 2050)
        ];
    }
}
