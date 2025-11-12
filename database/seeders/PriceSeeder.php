<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Carbon\Carbon;
use App\Models\GameTime;

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

        // Ensure Prices exist for the latest GameTime for all stocks
        $this->ensureLatestPrices();
    }


    private function defaultDate()
    {
        $stocks = Stock::all(); // Holt alle Stocks aus der Datenbank

        foreach ($stocks as $stock)
        {
            $year = 2000;
            $month = 1;
            $gtService = new \App\Services\GameTimeService();
            for ($i = 0; $i < 132; $i++) // 11 Jahre * 12 Monate = 132 Monate
            {
                $timestamp = mktime(0, 0, 0, $month, 1, year: $year);
                $currentDate = date('Y-m-d', $timestamp);

                $price = new Price(); // Neues Price-Objekt für jede Iteration
                $price->stock_id = $stock->id;
                // Erstelle oder hole GameTime für das aktuelle Datum
                $gameTime = $gtService->getOrCreate(Carbon::parse($currentDate));
                $price->game_time_id = $gameTime->id;
                $price->name = fake()->randomFloat(2, 1, 100);
                $price->save(); // Speichere das Price-Objekt in der Datenbank

                // Gehe zum nächsten Monat
                $month++;
                if ($month > 12) {
                    $month = 1;
                    $year++;
                }
            }
        }

        return true; // Rückgabe, falls benötigt
    }

    private function ensureLatestPrices()
    {
        $stocks = Stock::all();
        $latestGameTime = GameTime::getCurrentGameTime()->name;

        if (!$latestGameTime) {
            // If no GameTime exists, create one for the last GameTime in the range (2010-12-01)
            $gtService = new \App\Services\GameTimeService();
            $latestGameTime = GameTime::getCurrentGameTime();// $gtService->getOrCreate('2010-12-01');
        }

        foreach ($stocks as $stock) {
            $existingPrice = Price::where('stock_id', $stock->id)
                ->where('game_time_id', $latestGameTime->id)
                ->first();

            if (!$existingPrice) {
                // Create a new Price with a generated value
                $price = new Price();
                $price->stock_id = $stock->id;
                $price->game_time_id = $latestGameTime->id;
                $price->name = fake()->randomFloat(2, 1, 100); // Generate a random price
                $price->save();
            }
        }
    }
}

