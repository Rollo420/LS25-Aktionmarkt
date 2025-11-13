<?php

namespace Database\Factories;

use App\Models\GameTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameTime>
 */
class GameTimeFactory extends Factory
{
    /**
     * Static counters für Jahr und Monat (behalten Zustand zwischen Aufrufen)
     */
    private static $year = 2000;
    private static $month = 1;

    /**
     * Definiere den Standardzustand des Models
     */
    public function definition(): array
    {
        $dateString = sprintf('%04d-%02d-01', self::$year, self::$month);
        $date = Carbon::parse($dateString);

        // Erhöhe Monat und Jahr für nächste Factory-Aufrufe
        self::$month++;
        if (self::$month > 12) {
            self::$month = 1;
            self::$year++;
        }

        // Reset wenn das Datum 2010-12-01 erreicht ist
        if (self::$year == 2011 && self::$month == 1) {
            self::$year = 2000;
            self::$month = 1;
        }

        return ['name' => $date->toDateString()];
    }
}
