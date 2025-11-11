<?php

namespace App\Services;

use App\Http\Responses\Dividende;
use App\Models\Stock\Stock;
use Carbon\Carbon;

class DividendeService
{
    public function getDividendeForStockID(int $stockId): ?Dividende
    {
        $stock = Stock::find($stockId);
        if (!$stock) {
            return null;
        }

        return $this->getDividendStatisticsForStock($stock);
    }

    public function getDividendStatisticsForStock(Stock $stock, $user = null): Dividende
    {
        if (!$stock) {
            return new Dividende();
        }

        $dividend = $stock->getLatestDividend();

        if (!$dividend) {
            return new Dividende();
        }

        $price = $stock->getLatestPrice();
        $amount = $dividend->amount_per_share;
        $percent = $price > 0 ? ($amount / $price) * 100 : 0; // Dividendenrendite in %

        $firstBuyDate = $stock->getFirstBuyTransactionDateForStock();
        $total_dividends = 0;

        if (!is_null($firstBuyDate)) {
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
        $dividende->frequency_per_year = $stock->dividend_frequency ?? 0;
        $dividende->total_received = $total_dividends;
        $dividende->expected_next_12m = $dividende->last_amount * ($stock->dividend_frequency ?? 0) * $stock->getCurrentQuantity($user);
        $dividende->yield_percent = $dividende->dividendPercent;

        return $dividende;
    }

    public function shareDividendeToUsers(Stock $stock)
    {

        $userAccounts = $stock->getUserAccount();
        $userAccounts->map(function ($user) use ($stock) {

            $quantity = $stock->getCurrentQuantity($user);
            $dividend_per_share = $stock->getCurrentDividend();
            $total_dividend = $quantity * $dividend_per_share;
            if ($total_dividend > 0) {
                $user->addBankAccountBalance($total_dividend);
            }

        });
        


    }
    public function calculateNextDividendDate()
    {

    }
}
