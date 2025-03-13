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
    protected static $stock_id = 1;
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomFloat(2, 0, 100),            
            'month' => fake()->monthName(),
            'stock_id' => $this->defaultStock(), //fake()->numberBetween(1,5)
            //'year' => self::$yearDate++
        ];
    }

    private function defaultStock() 
    {
        if (self::$stock_id != 6)
        {
            return self::$stock_id++;
        }

        return fake()->numberBetween(1, 5);
    }
}
