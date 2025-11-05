<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock\Stock;
use App\Models\Dividend;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 5 Stocks erstellen
        $stocks = Stock::factory(5)->create();

        foreach ($stocks as $stock) {
            $frequency = $stock->dividend_frequency;
            $frequency = ($frequency && $frequency > 0) ? $frequency : 4; // fallback auf 4, wenn null oder 0

            $monthsBetween = (int) (12 / $frequency);

            //Auch die jahre plus rechenen if anweisung fallback 
            

            for ($i = 0; $i < $frequency; $i++) {
                $gtService = new \App\Services\GameTimeService();
                $now = now();
                $gt = $gtService->getOrCreate(Carbon::create((int)$now->format('Y'), (int)$now->format('m'), 1));
                Dividend::create([
                    'stock_id' => $stock->id,
                    'game_time_id' => $gt->id,
                    'amount_per_share' => $faker->randomFloat(2, 0.5, 2.5),
                ]);

            }
        }
    }
}
