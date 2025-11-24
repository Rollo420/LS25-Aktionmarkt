<?php

namespace App\Services;

use App\Http\Responses\Dividende;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

        // get current game time (if available) so we can query historical/dividend data at that time
        $gameTime = (new GameTime()->getCurrentGameTime());
        

        \Log::debug("DividendeService - Stock ID: {$stock->id}, dividend_frequency: " . ($stock->dividend_frequency ?? 'null'));

        $dividend = $stock->getDividendAtGameTime($gameTime);
        
        if (!$dividend) {
            \Log::warning("DividendeService - No dividend found for stock ID: {$stock->id}");
            return new Dividende();
        }

        \Log::debug("DividendeService - Dividend amount_per_share: " . $dividend->amount_per_share);

        $price = $gameTime ? $stock->getPriceAtGameTime($gameTime) : $stock->getLatestPrice();
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
        $dividende->next_date = $stock->calculateNextDividendDateAtGameTime($gameTime) ? $stock->calculateNextDividendDateAtGameTime($gameTime)->format('d.m.Y') : null;
        $dividende->next_amount = $dividend->amount_per_share ?? 0;
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
        \Log::info("Starting dividend payout for stock: {$stock->name} (ID: {$stock->id})");

        $gt = new GameTime();
        $userAccounts = $stock->getUserAccount();
        $totalUsers = $userAccounts->count();
        $totalPayout = 0;
        $successfulPayouts = 0;

        \Log::info("Found {$totalUsers} users with holdings for stock {$stock->name}");

        $userAccounts->map(function ($user) use ($stock, $gt, &$totalPayout, &$successfulPayouts) {

            $quantity = $stock->getCurrentQuantity($user);
            if ($quantity <= 0) {
                \Log::debug("User {$user->id} has no holdings for stock {$stock->id}, skipping dividend");
                return; // Keine Dividende, wenn keine Aktien vorhanden
            }

            $dividend_per_share = $stock->getCurrentDividendAmount();
            $total_dividend = $quantity * $dividend_per_share;

            \Log::info("User {$user->id}: quantity={$quantity}, dividend_per_share={$dividend_per_share}, total_dividend={$total_dividend}");

            if ($total_dividend > 0) {
                try {
                    $oldBalance = $user->getBankAccountBalance();
                    $user->addBankAccountBalance($total_dividend);
                    $newBalance = $user->getBankAccountBalance();
                    \Log::info("User {$user->id} balance updated: {$oldBalance} -> {$newBalance} (+{$total_dividend})");

                    $transaction = $user->transactions()->create([
                        'type' => 'dividend',
                        'stock_id' => $stock->id,
                        'quantity' => $quantity,
                        'price_at_buy' => $dividend_per_share,
                        'status' => false, // closed - sofort ausgezahlt
                        'game_time_id' => $gt->getCurrentGameTime()->id,
                    ]);

                    if ($transaction) {
                        \Log::info("Dividend transaction created for user {$user->id}: ID={$transaction->id}, type=dividend, stock={$stock->id}, quantity={$quantity}, amount={$total_dividend}");
                        $totalPayout += $total_dividend;
                        $successfulPayouts++;
                    } else {
                        \Log::error("Failed to create dividend transaction for user {$user->id}");
                    }
                } catch (\Exception $e) {
                    \Log::error("Error during dividend payout for user {$user->id}: " . $e->getMessage());
                }
            } else {
                \Log::warning("Total dividend for user {$user->id} is zero or negative, skipping payout");
            }
        });

        \Log::info("Dividend payout completed for stock {$stock->name}. Total payout: {$totalPayout} to {$successfulPayouts}/{$totalUsers} users");
    }

    
}
