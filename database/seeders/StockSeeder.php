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

        
        $nextGameTime = '2000-01-01';
        
        foreach ($stocks as $stock) {
            // Hole die nächste oder aktuelle GameTime-ID
            if($nextGameTime != '2000-01-01')
                $nextGameTime = $stock->calculateNextDividendDate();
            
            $gameTimeId = $gtService->getOrCreate($nextGameTime);
            
            // ✅ Dividende erzeugen
            $dividende = Dividend::factory()->create([
                'stock_id' => $stock->id,
                'game_time_id' => $gameTimeId->id,
                'amount_per_share' => $faker->randomFloat(2, 0.1, 5.0),
            ]);
            
            // ✅ Preisverläufe erzeugen
            $price = Price::factory(132)->create([
                'stock_id' => $stock->id,
            ]);

        }
    }
}
