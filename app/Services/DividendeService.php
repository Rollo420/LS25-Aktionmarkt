<?php
namespace App\Services;

use Carbon\Carbon;

use App\Models\Dividend;
use App\Models\Stock\Stock;

class DividendeService
{
    public function getDividendeForStock($stockId)
    {

        $stock = Stock::findOrFail($stockId);
        $dividend = $stock->dividends()->latest()->first(); // z.B. 2.5
        $percent = $dividend->amount_per_share; // z.B. 2.5

        $details['dividende'] = [
            'dividendPerShare' => $stock->getLatestPrice() * ($percent / 100), // Euro Dividende pro Aktie
            'dividendYield' => $percent, // Dividendenrendite in %
            'nextDividendDate' => Carbon::parse($dividend->distribution_date)->format(format: 'Y-m-d'),
        ];

        return $details;
    }

    public function getNextDividendDates()
    {
        $dividends = Dividend::orderBy('distribution_date');

        $stocks = $dividends->stock();

    }
}