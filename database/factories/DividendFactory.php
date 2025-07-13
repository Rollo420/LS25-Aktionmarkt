<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Stock\Stock;
use App\Models\Dividend;

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
        $stock = Stock::pluck('id')->random();
        
        if(Dividend::where('stock_id', $stock->id)->count('id') > 0)
        {
            //
        }



        return [
            'stock_id' => $stock->id,
        ];
    }
}
