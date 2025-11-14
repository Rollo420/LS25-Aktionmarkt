<?php

namespace App\Services;

use App\Http\Responses\Dividende;
use Carbon\Carbon;

use App\Services\GameTimeService;

use App\Models\GameTime;
use App\Models\Stock\Stock;
use App\Models\Dividend;

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
        $dividende->next_date = $stock->calculateNextDividendDate() ? $stock->calculateNextDividendDate()->format('d.m.Y') : null;
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
        
        $gt = new GameTime();
        $userAccounts = $stock->getUserAccount();
        $userAccounts->map(function ($user) use ($stock, $gt) {
            
            $quantity = $stock->getCurrentQuantity($user);
            $dividend_per_share = $stock->getCurrentDividendAmount();
            $total_dividend = $quantity * $dividend_per_share;
            if ($total_dividend > 0) {
                $user->addBankAccountBalance($total_dividend);
            }

            $user->transactions()->create([
                'type' => 'dividend',
                'stock_id' => $stock->id,
                'quantity' => -$quantity,
                'price_at_buy' => $dividend_per_share,
                'status' => true,
                'game_time_id' => $gt->getCurrentGameTime()->id,
            ]);
        });
        
    }
}
