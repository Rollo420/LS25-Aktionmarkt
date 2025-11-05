<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GameTime;
use Carbon\Carbon;

class GameTimeSeeder extends Seeder
{
    public function run(): void
    {
        // Create GameTime entries for each month from 2000 to 2010 using mktime
        $year = 2000;
        $month = 1;

        while ($year < 2011 || ($year == 2010 && $month <= 12)) {
            $timestamp = mktime(0, 0, 0, $month, 1, $year);
            $dateString = date('Y-m-d', $timestamp);

            GameTime::firstOrCreate([
                'name' => $dateString,
            ]);

            $month++;
            if ($month > 12) {
                $month = 1;
                $year++;
            }
        }
    }
}
