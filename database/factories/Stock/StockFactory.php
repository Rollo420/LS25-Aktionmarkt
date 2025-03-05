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
            'price_id' => self::$priceId++,
            'product_type_id' => self::$productTypeId++,
        ];
    }
}
