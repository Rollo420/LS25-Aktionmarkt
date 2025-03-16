<?php

namespace Database\Factories\Stock;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\Stock\Price;
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
        $this->getLastDate();
        return [
            'name' =>  20,            
            'month' => date("m", $d1),
            'stock_id' => 1,
            'year' => date('y', $d1)
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

    public function getLastDate()
    {
        $price = Price::latest()->where('stock_id', $this->stock_id);
        dd($price);
        return $price;
    }

}
