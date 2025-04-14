<?php

namespace Database\Factories\Stock;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock\Stock>
 */
class StockFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'frima' => fake()->word(),
            'sector' => fake()->word(),
            'land' => fake()->word(),
            'description' => fake()->text(),
            'net_income' => fake()->randomFloat(2, 1000, 1000000),            
        ];
    }

    private function ListOfPriceBetween($created, $int1 = 1, $int2 = 5)
    {
        for ($i=0; $created > $i; $i++)
        {
            yield fake()->numberBetween($int1, $int2);
        }

    }
}
