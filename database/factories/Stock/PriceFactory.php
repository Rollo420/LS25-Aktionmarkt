<?php

namespace Database\Factories\Stock;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock\price>
 */
class PriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
    protected static $yearDate = 1;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomFloat(2, 0, 100),            
            'month' => fake()->monthName(),
            //'year' => self::$yearDate++
        ];
    }
}
