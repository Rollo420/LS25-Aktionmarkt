<?php

namespace Database\Factories\Stock;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock\Stock>
 */
class StockFactory extends Factory
{
    protected static $productTypeId = 1;
    protected static $priceId = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price_id' => fake()->numberBetween(1,5),//$this->ListOfPriceBetween(50, 1, 5),
            'product_type_id' => fake()->numberBetween(1, 5),
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
