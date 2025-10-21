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
            $gtService = new \App\Services\GameTimeService();
            for ($i = 0; $i <= 50; $i++)
            {
                $price = new Price(); // Neues Price-Objekt für jede Iteration
                $price->stock_id = $stock->id;
                // ensure a game_time exists using service (create/dedupe)
                // cycle months forward from start year 2000
                [$y, $m] = explode('-', $defaultDate);
                $gameTime = $gtService->getOrCreate((int)$y, (int)$m);
                $price->game_time_id = $gameTime->id;
                // V2: we no longer write a separate 'date' column — use game_time_id/created_at instead
                // keep defaultDate progression for historic-like values if needed in the future
                $defaultDate = date("Y-m-d", strtotime($defaultDate . ' +1 month'));
                $price->name = fake()->randomFloat(2, 1, 100);
                $price->save(); // Speichere das Price-Objekt in der Datenbank
            }
        }

        return true; // Rückgabe, falls benötigt
    }
}

