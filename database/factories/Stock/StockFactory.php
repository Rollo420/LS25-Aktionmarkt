<?php

namespace Database\Factories\Stock;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\ProductType;
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
            'product_type_id' => ProductType::inRandomOrder()->first()?->id ?? ProductType::factory()->create()->id,
            'name' => fake()->word(),
            'firma' => fake()->word(),
            'sektor' => fake()->word(),
            'land' => fake()->word(),
            'description' => fake()->text(),
            'net_income' => fake()->randomFloat(2, 1000, 1000000),
            'dividend_frequency' => rand(0,4),
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
