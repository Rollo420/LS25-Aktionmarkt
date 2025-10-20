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

        // Dann GameTime für 12 Jahre erstellen
        $currentMonthIndex = 1; // optionale Laufvariable für aktuelle Ingame-Zeit
        for ($year = 1; $year <= 12; $year++) {
            foreach ($monthNames as $index => $monthName) {
                GameTime::firstOrCreate(
                    [
                        'current_year' => $year,
                        'month_id' => $index + 1, // 1–12
                    ],
                );
                $currentMonthIndex++;
            }
        }
    }
}
