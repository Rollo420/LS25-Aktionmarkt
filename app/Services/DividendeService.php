<?php

namespace App\Services;

use App\Http\Responses\Dividende;
use App\Models\Stock\Stock;
use Carbon\Carbon;

class DividendeService
{
    public function getDividendeForStockID(int $stockId): ? Dividende
    {
        $stock = Stock::find($stockId);
        if (!$stock) {
            return null;
        }

        return $this->getDividendStatisticsForStock($stock);
    }

    public function getDividendStatisticsForStock(Stock $stock): Dividende
    {
        $dividend = $stock->getLatestDividend();

        if (!$dividend) {
            return new Dividende();
        }

        $price = $stock->getLatestPrice();
        $amount = $dividend->amount_per_share;
        $percent = $price > 0 ? ($amount / $price) * 100 : 0; // Dividendenrendite in %

        $firstBuyDate = $stock->getFirstBuyTransactionDateForStock();
        $total_dividends = 0;

        if(!is_null($firstBuyDate)){
            $total_dividends = $stock->dividends()
                ->whereHas('gameTime', function ($query) use ($firstBuyDate) {
                    $query->where('name', '>=', $firstBuyDate);
                })
                ->sum('amount_per_share');
        }

        $dividende = new Dividende();

        $dividende->dividendPerShare = round($amount, 2);
        $dividende->dividendPercent = round($percent, 2); // Prozentwert
        $dividende->next_date = $stock->getNextDividendDate() ? Carbon::parse($stock->getNextDividendDate())->format('d.m.Y') : null;
        $dividende->next_amount = $stock->getLatestDividend()->amount_per_share ?? 0;
        $dividende->last_date = Carbon::parse($dividend->gameTime->name)->format('d.m.Y');
        $dividende->last_amount = $dividend->amount_per_share;
        $dividende->frequency_per_year = $stock->dividend_frequency;
        $dividende->total_received = $total_dividends;
        $dividende->expected_next_12m = $dividende->last_amount * $stock->dividend_frequency * $stock->getCurrentQuantity();
        $dividende->yield_percent = $dividende->dividendPercent;

        return $dividende;
    }

    /**
     * Berechnet und führt Dividendenausschüttung für alle User durch
     * Gibt ein Array mit den Ausschüttungsdetails zurück
     */
    public function payoutDividends($currentGameTime = null): array
    {
        $results = [
            'total_users_paid' => 0,
            'total_amount_paid' => 0.0,
            'dividends_paid' => []
        ];

        // Hole alle User mit Bankkonten
        $users = \App\Models\User::with('bank')->get();

        foreach ($users as $user) {
            $userDividends = $this->calculateUserDividends($user, $currentGameTime);

            if ($userDividends['total_amount'] > 0) {
                // Guthaben auf Bankkonto gutschreiben
                $user->bank->balance += $userDividends['total_amount'];
                $user->bank->save();

                $results['total_users_paid']++;
                $results['total_amount_paid'] += $userDividends['total_amount'];
                $results['dividends_paid'][] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'total_amount' => $userDividends['total_amount'],
                    'stock_dividends' => $userDividends['stock_dividends']
                ];
            }
        }

        return $results;
    }

    /**
     * Berechnet Dividenden für einen einzelnen User
     */
    public function calculateUserDividends(\App\Models\User $user, $currentGameTime = null): array
    {
        $result = [
            'total_amount' => 0.0,
            'stock_dividends' => []
        ];

        // Hole alle Aktien des Users mit aktuellen Holdings
        $userStocks = \App\Services\StockService::getUserStocksWithHoldings($user);

        foreach ($userStocks as $stockData) {
            $stock = $stockData['stock'];
            $quantity = $stockData['quantity'] ?? 0;

            if ($quantity > 0 && $currentGameTime) {
                // Hole alle Dividenden für diesen Stock, die zum aktuellen GameTime passen
                $dividends = $stock->dividends()->whereHas('gameTime', function ($query) use ($currentGameTime) {
                    $query->where('name', $currentGameTime->name);
                })->get();

                foreach ($dividends as $dividend) {
                    // Berechne Dividendenausschüttung
                    $dividendAmount = $dividend->amount_per_share * $quantity;

                    $result['total_amount'] += $dividendAmount;
                    $result['stock_dividends'][] = [
                        'stock_id' => $stock->id,
                        'stock_name' => $stock->name,
                        'quantity' => $quantity,
                        'dividend_per_share' => $dividend->amount_per_share,
                        'total_dividend' => $dividendAmount
                    ];
                }
            }
        }

        return $result;
    }
}


