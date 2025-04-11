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
    
    protected static $stock_id = 1;
     
    public function definition(): array
    {
        
        return [
            'name' =>  20,            
            'date' => $this->defaultDate(),
            'stock_id' => 1,
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
