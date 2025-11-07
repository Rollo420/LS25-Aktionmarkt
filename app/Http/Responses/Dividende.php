<?php

namespace App\Http\Responses;

use Carbon\Carbon;

class Dividende
{
    public float $dividendPerShare;
    public float $dividendPercent;
    public ?string $next_date;
    public float $next_amount;
    public ?string $last_date;
    public float $last_amount;
    public int $frequency_per_year;
    public float $total_received;
    public float $expected_next_12m;
    public float $yield_percent;

   

}
