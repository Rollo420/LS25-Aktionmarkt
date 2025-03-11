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
            'name' => fake()->word()
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
