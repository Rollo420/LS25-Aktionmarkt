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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // For sequential creation, but factory can still be used for random if needed
        // Default to random for flexibility
        return [
            'name' => $this->faker->dateTimeBetween('2000-01-01', '2010-12-31')->format('Y-m-d'),
        ];
    }

    /**
     * Create sequential GameTimes from 2000-01 to 2010-12
     */
    public function sequential()
    {
        static $year = 2000;
        static $month = 1;

        $timestamp = mktime(0, 0, 0, $month, 1, $year);
        $date = date('Y-m-d', $timestamp);

        $month++;
        if ($month > 12) {
            $month = 1;
            $year++;
        }

        return [
            'name' => $date,
        ];
    }
}
