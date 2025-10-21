<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GameTime;
use App\Models\Month;

class GameTimeSeeder extends Seeder
{
    public function run(): void
    {
        // Zuerst die Monate einmalig erstellen
        $monthNames = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        foreach ($monthNames as $name) {
            Month::firstOrCreate(['name' => $name]);
        }

        // Dann GameTime für einen realistischen Zeitraum erstellen (z.B. Jahre 2000–2010)
        $startYear = 2000;
        $endYear = 2010;
        for ($year = $startYear; $year <= $endYear; $year++) {
            foreach ($monthNames as $index => $monthName) {
                GameTime::firstOrCreate([
                    'current_year' => $year,
                    'month_id' => $index + 1, // 1–12
                ]);
            }
        }
    }
}
