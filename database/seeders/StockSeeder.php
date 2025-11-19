<?php

namespace Database\Seeders;

use App\Services\GameTimeService;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Dividend;
use App\Models\Stock\Stock;
use App\Models\Stock\Price;
use App\Models\GameTime;
use App\Models\Config;

use App\Http\Controllers\TimeController;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Collection\Collection;

class StockSeeder extends Seeder
{
    public function run(): \Illuminate\Database\Eloquent\Collection
    {
        // Call BT21StockSeeder to get BT21 stocks
        $bt21Seeder = new BT21StockSeeder();
        $bt21Stocks = $bt21Seeder->run();

        // Create other stocks
        $otherStocks = Stock::factory(3)->create();
        // Combine the collections

        $allStocks = $bt21Stocks->merge($otherStocks);
        
        // Now, proceed with the rest of the logic for prices and dividends
        $faker = Faker::create();
        $gt = new GameTime();
        $gtService = new GameTimeService();
        $timeController = new TimeController();
        $config = Config::factory()->create(['name' => 'Default Config', 'description' => 'Dieses ist die standart Einstellungen'])->get()->first();
        
        // Erstelle 132 GameTimes von 2000-01-01 bis 2010-12-01
        $currentDate = Carbon::parse('2000-01-01');
        $gameTimes = [];
        for ($i = 0; $i < 132; $i++) {
            $gameTimes[] = $gtService->getOrCreate($currentDate);
            $currentDate = $currentDate->addMonth();
        }
        
        foreach ($bt21Stocks as $stock) {
            $lastPrice = 100.0; // Initialer Preis
            
            foreach ($gameTimes as $gt) {
                $monthIndex = (int) date('m', strtotime($gt->name)) - 1; // 0-based
                $newPrice = $timeController->generatePrice($lastPrice, $monthIndex);
                
                Price::create([
                    'stock_id' => $stock->id,
                    'game_time_id' => $gt->id,
                    'name' => $newPrice,
                ]);

                $lastPrice = $newPrice;
                
                // Dividende für den ersten GameTime erstellen
                Dividend::factory()->create([
                    'stock_id' => $stock->id,
                    'game_time_id' => $gt->id,
                    'amount_per_share' => $faker->randomFloat(2, 0.1, 5.0),
                ]);
            }

            $stock->configs()->attach($config->id);
        }

        return $allStocks;
    }
}
