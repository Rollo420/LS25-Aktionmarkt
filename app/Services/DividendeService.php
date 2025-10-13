<?php
namespace App\Services;

use App\Models\Stock\Stock;
use Carbon\Carbon;

class DividendeService
{
    public function getDividendeForStock($stockId)
    {

        $stock = Stock::findOrFail($stockId);
        $dividend = $stock->dividends()->latest()->first(); // z.B. 2.5
        $percent = $dividend->amount_per_share; // z.B. 2.5

        $details['dividende'] = [
            'dividendPerShare' => $stock->latestPrice() * ($percent / 100), // Euro Dividende pro Aktie
            'dividendYield' => $percent, // Dividendenrendite in %
            'nextDividendDate' => Carbon::parse($dividend->distribution_date)->format(format: 'Y-m-d'),
        ];

        return $details;
    }
}