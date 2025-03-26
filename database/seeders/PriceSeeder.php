<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder; 
use \App\Models\Stock\Price;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->getLastDate();

    }

    public function getLastDate()
    {
        $price = new Price();
        $price->stock_id = 1;
        $stock_id = $price->stock_id;
        $price = Price::latest()->where('stock_id', $stock_id);
        return $price;
    }
}

