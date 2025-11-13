<?php

namespace Database\Seeders;

use App\Services\GameTimeService;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Dividend;
use App\Models\Stock\Stock;
use App\Models\Stock\Price;
use App\Models\GameTime;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $gt = new GameTime();
        $gtService = new GameTimeService();

        // 5 Stocks erstellen
        $stocks = Stock::factory(5)->create();

        
        
        foreach ($stocks as $stock) {

            $nextGameTime = $stock->calculateNextDividendDate();

            if(is_null($nextGameTime)) {
                $nextGameTime = $stock->calculateNextDividendDate($gt->getCurrentGameTime()->name);
            }
            
            $gameTimeId = $gtService->getOrCreate($nextGameTime);
            
            // ✅ Preisverläufe erzeugen
            $price = Price::factory(132)->create([
                'stock_id' => $stock->id,
            ]);
            
            // ✅ Dividende erzeugen
            $dividende = Dividend::factory()->create([
                'stock_id' => $stock->id,
                'game_time_id' => $gameTimeId->id,
                'amount_per_share' => $faker->randomFloat(2, 0.1, 5.0),
            ]);
        }
    }
}
