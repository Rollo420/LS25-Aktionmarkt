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
    

    /**
     * Definiere den Standardzustand des Models
     */
    public function definition(): array
    {
        $year = 2000;
        $month = 1;

        // Erstelle das aktuelle Datum
        $firstDate = Carbon::parse(GameTime::getCurrentGameTime()->name);

        
        return $firstDate->addMonth(1);
    }
}
