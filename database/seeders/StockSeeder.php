<?php

namespace Database\Seeders;

use App\Services\GameTimeService;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Dividend;
use App\Models\Stock\Stock;
use App\Models\Stock\Price;
use App\Models\GameTime;
use App\Http\Controllers\TimeController;
use Carbon\Carbon;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $gt = new GameTime();
        $gtService = new GameTimeService();
        $timeController = new TimeController();

        // 5 Stocks erstellen
        $stocks = Stock::factory(5)->create();

        // Erstelle 132 GameTimes von 2000-01-01 bis 2010-12-01
        $currentDate = Carbon::parse('2000-01-01');
        $gameTimes = [];
        for ($i = 0; $i < 132; $i++) {
            $gameTimes[] = $gtService->getOrCreate($currentDate);
            $currentDate = $currentDate->addMonth();
        }

        foreach ($stocks as $stock) {
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
        }
    }
}
