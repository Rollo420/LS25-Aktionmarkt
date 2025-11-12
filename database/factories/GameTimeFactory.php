<?php

namespace Database\Factories;

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
    protected static int $year = 2000;
    protected static int $month = 1;

    /**
     * Definiere den Standardzustand des Models
     */
    public function definition(): array
    {
        // Erstelle das aktuelle Datum
        $date = Carbon::create(self::$year, self::$month, 1)->format('Y-m-d');

        // Erhöhe den Monat für den nächsten Factory-Aufruf
        self::$month++;
        if (self::$month > 12) {
            self::$month = 1;
            self::$year++;
        }

        return [
            'name' => $date,
        ];
    }
}
