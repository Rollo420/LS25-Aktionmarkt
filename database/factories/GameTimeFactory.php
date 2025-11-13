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
    public function definition(): array
    {
        // Hole das aktuellste GameTime-Datum
        $current = GameTime::getCurrentGameTime();

        // Wenn vorhanden, einen Monat draufrechnen, sonst mit 2000-01-01 starten
        $firstDate = $current
            ? Carbon::parse($current->name)->addMonth()
            : Carbon::create(2000, 1, 1);

        // 🔥 Hier den formatierten String zurückgeben, nicht das Carbon-Objekt
        return [
            'name' => $firstDate->format('Y-m-d'),
        ];
    }
}
