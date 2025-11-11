<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

use App\Models\Dividend;
use App\Models\Stock\Stock;
use App\Models\GameTime;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $gt = new GameTime();

        // 5 Stocks erstellen
        $stocks = Stock::factory(5)->create();

        foreach ($stocks as $stock) {
            // Erstelle ersten Dividend mit calculateNextDividendDate (da noch kein Dividend vorhanden, wird null zurückgegeben, also setze auf aktuelles GameTime)
            $currentGameTime = $gt->getCurrentGameTime();
            Dividend::create([
                'stock_id' => $stock->id,
                'game_time_id' => $currentGameTime->id,
                'amount_per_share' => $faker->randomFloat(2, 0.1, 5.0),
            ]);

            // Erstelle weitere Dividenden mit calculateNextDividendDate
            $currentStock = $stock->fresh();
            for ($i = 0; $i < 4; $i++) { // Erstelle bis zu 4 weitere Dividenden
                $nextDate = $currentStock->calculateNextDividendDate();
                if ($nextDate) {
                    $gtService = new \App\Services\GameTimeService();
                    $gt = $gtService->getOrCreate($nextDate);
                    Dividend::create([
                        'stock_id' => $stock->id,
                        'game_time_id' => $gt->id,
                        'amount_per_share' => $faker->randomFloat(2, 0.1, 5.0),
                    ]);
                    $currentStock = $stock->fresh();
                } else {
                    break;
                }
            }
        }
    }
}
