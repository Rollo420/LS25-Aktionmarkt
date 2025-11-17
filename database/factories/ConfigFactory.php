<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Config>
 */
class ConfigFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'volatility_range' => $this->faker->randomFloat(2, 0.01, 0.1),
            'seasonal_effect_strength' => $this->faker->randomFloat(2, 0.01, 0.05),
            'crash_probability_monthly' => $this->faker->randomFloat(2, 0.5, 2),
            'crash_interval_months' => $this->faker->numberBetween(120, 360),
            'rally_probability_monthly' => $this->faker->randomFloat(2, 0.5, 2),
            'rally_interval_months' => $this->faker->numberBetween(180, 480),
        ];
    }
}
