<?php

namespace App\Services;

use App\Models\Stock\Stock;
use Carbon\Carbon;

class DividendeService
{
    public function getDividendeForStockID(int $stockId): ?array
    {
        $stock = Stock::find($stockId);
        if (!$stock) {
            return null;
        }

        return $this->getDividendStatisticsForStock($stock);
    }

    public function getDividendStatisticsForStock(Stock $stock): array
    {
        $dividend = $stock->getLatestDividend();

        if (!$dividend) {
            return [
                'dividende' => [
                    'dividendPerShare' => 0,
                    'dividendPercent' => 0,
                    'nextDividendDate' => null,
                ],
            ];
        }

        $price = $stock->getLatestPrice();
        $amount = $dividend->amount_per_share;
        $percent = $price > 0 ? ($amount / $price) * 100 : 0; // Dividendenrendite in %

        return [
            'dividende' => [
                'dividendPerShare' => round($amount, 2),
                'dividendPercent' => round($percent, 2), // Prozentwert
                'nextDividendDate' => Carbon::parse($dividend->distribution_date)->format('Y-m-d'),
            ],
        ];
    }
}
