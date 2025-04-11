<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder; 
use \App\Models\Stock\Price;
use App\Models\Stock\Stock;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $result = $this->defaultDate();
        
        
    }


    private function defaultDate() 
    {
        $stocks = Stock::all(); // Holt alle Stocks aus der Datenbank
        

        foreach ($stocks as $stock) 
        {            
            $defaultDate = '2000-01-01'; // Startdatum im richtigen Format
            for ($i = 0; $i <= 50; $i++)
            {
                $price = new Price(); // Neues Price-Objekt für jede Iteration
                $price->stock_id = $stock->id;
                $price->date = date("Y-m-d", strtotime($defaultDate)); // Korrektes Datumsformat
                $defaultDate = date("Y-m-d", strtotime($defaultDate . ' +1 month')); // Nächstes Datum
                $price->name = fake()->randomFloat(2, 1, 100);
                $price->save(); // Speichere das Price-Objekt in der Datenbank
            }
        }

        return true; // Rückgabe, falls benötigt
    }
}

