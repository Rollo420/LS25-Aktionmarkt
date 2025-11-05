<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DividendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stocks = \App\Models\Stock\Stock::all();
        $gtService = new \App\Services\GameTimeService();

        foreach ($stocks as $stock) {
            $year = 2000;
            $month = 1;
            for ($i = 0; $i < 132; $i++) { // 11 Jahre * 12 Monate = 132 Monate
                $timestamp = mktime(0, 0, 0, $month, 1, $year);
                $currentDate = date('Y-m-d', $timestamp);

                // Create dividend occasionally, e.g., every 12 months or randomly
                if ($i % 12 == 0 && fake()->boolean(30)) { // 30% chance every year
                    $dividend = new \App\Models\Dividend();
                    $dividend->stock_id = $stock->id;
                    $gameTime = $gtService->getOrCreate(\Carbon\Carbon::parse($currentDate));
                    $dividend->game_time_id = $gameTime->id;
                    $dividend->amount_per_share = fake()->randomFloat(2, 0.1, 2.0);
                    $dividend->save();
                }

                // Advance to next month
                $month++;
                if ($month > 12) {
                    $month = 1;
                    $year++;
                }
            }
        }
    }
}
