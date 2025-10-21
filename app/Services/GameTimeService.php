<?php

namespace App\Services;

use App\Models\GameTime;
use Carbon\Carbon;

class GameTimeService
{
    /**
     * Get or create a GameTime for the given year and month.
     * Ensures uniqueness via firstOrCreate.
     *
     * @param int $year
     * @param int $month
     * @return GameTime
     */
    public function getOrCreate(int $year, int $month): GameTime
    {
        $normalizedMonth = (($month - 1) % 12) + 1;
        $normalizedYear = $year + intdiv($month - 1, 12);

        return GameTime::firstOrCreate([
            'month_id' => $normalizedMonth,
            'current_year' => $normalizedYear,
        ]);
    }

    /**
     * Advance a month/year pair by $steps months and return the new pair.
     *
     * @param int $year
     * @param int $month
     * @param int $steps
     * @return array [year, month]
     */
    public function advanceMonths(int $year, int $month, int $steps = 1): array
    {
        $total = ($year * 12 + ($month - 1)) + $steps;
        $newYear = intdiv($total, 12);
        $newMonth = ($total % 12) + 1;
        return [$newYear, $newMonth];
    }

    /**
     * Return a Carbon date representing the start of the given GameTime (year-month-01)
     */
    public function toDate(GameTime $gt): Carbon
    {
        return Carbon::createFromDate($gt->current_year ?? date('Y'), $gt->month_id ?? 1, 1);
    }
}
