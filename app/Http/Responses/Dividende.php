<?php

namespace App\Http\Responses;

use Carbon\Carbon;

class Dividende
{
    public float $dividendPerShare = 0.0;
    public float $dividendPercent = 0.0;
    public ?string $next_date = null;
    public float $next_amount = 0.0;
    public ?string $last_date = null;
    public float $last_amount = 0.0;
    public int $frequency_per_year = 0;
    public float $total_received = 0.0;
    public float $expected_next_12m = 0.0;
    public float $yield_percent = 0.0;

}
