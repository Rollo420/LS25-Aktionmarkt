<?php

namespace App\Services;

use App\Models\GameTime;
use Carbon\Carbon;

class GameTimeService
{
    /**
     * Get or create a GameTime for the given date.
     * Ensures uniqueness via firstOrCreate.
     *
     * @param Carbon $date
     * @return GameTime
     */
    public function getOrCreate(Carbon $date): GameTime
    {
        return GameTime::firstOrCreate([
            'name' => $date->toDateString(),
        ]);
    }

    /**
     * Get or create a GameTime for the given year and month using strtotime.
     * Ensures uniqueness via firstOrCreate.
     *
     * @param int $year
     * @param int $month
     * @return GameTime
     */
    public function getOrCreateByYearMonth(int $year, int $month): GameTime
    {
        $dateString = sprintf('%04d-%02d-01', $year, $month);
        return GameTime::firstOrCreate([
            'name' => $dateString,
        ]);
    }

    /**
     * Advance a date by $steps months using strtotime and return the new date.
     *
     * @param string $dateString
     * @param int $steps
     * @return string
     */
    public function advanceMonthsStrtotime(string $dateString, int $steps = 1): string
    {
        $timestamp = strtotime($dateString);
        for ($i = 0; $i < $steps; $i++) {
            $timestamp = strtotime('+1 month', $timestamp);
        }
        return date('Y-m-d', $timestamp);
    }

    /**
     * Advance a date by $steps months and return the new date.
     *
     * @param Carbon $date
     * @param int $steps
     * @return Carbon
     */
    public function advanceMonths(Carbon $date, int $steps = 1): Carbon
    {
        return $date->copy()->addMonths($steps);
    }

    /**
     * Return a Carbon date representing the given GameTime
     */
    public function toDate(GameTime $gt): Carbon
    {
        return Carbon::parse($gt->name);
    }
}
