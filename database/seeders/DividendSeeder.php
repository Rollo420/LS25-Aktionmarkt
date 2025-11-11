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
        $gt = new \App\Models\GameTime();

        $currentGameTime = $gt->getCurrentGameTime();
        $currentDate = \Carbon\Carbon::parse($currentGameTime->name);

        foreach ($stocks as $stock) {
            // Erstelle ersten Dividend mit dem aktuellen GameTime
            \App\Models\Dividend::create([
                'stock_id' => $stock->id,
                'game_time_id' => $currentGameTime->id,
                'amount_per_share' => fake()->randomFloat(2, 0.1, 2.0),
            ]);

            // Erstelle weitere Dividenden mit calculateNextDividendDate
            $currentStock = $stock->fresh(); // Refresh um neuen Dividend zu laden
            for ($i = 0; $i < 4; $i++) { // Erstelle bis zu 4 weitere Dividenden
                $nextDate = $currentStock->calculateNextDividendDate();
                if ($nextDate) {
                    $gameTime = $gtService->getOrCreate($nextDate);
                    \App\Models\Dividend::create([
                        'stock_id' => $stock->id,
                        'game_time_id' => $gameTime->id,
                        'amount_per_share' => fake()->randomFloat(2, 0.1, 2.0),
                    ]);
                    $currentStock = $stock->fresh(); // Refresh wieder
                } else {
                    break;
                }
            }
        }
    }
}
