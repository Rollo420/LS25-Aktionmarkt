<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Carbon\Carbon;

use App\Services\DividendeService;

use App\Models\Stock\Stock;
use App\Models\Stock\Transaction;
use App\Models\BuyTransaction;

class StockService
{
    /**
     * Gesamtwert des Portfolios inkl. Bankguthaben - Optimierte Version
     */
    public function getTotalPortfolioValue(): float
    {
        $user = Auth::user();
        $bankBalance = $user->bank?->balance ?? 0;

        // Optimierte Berechnung mit einer einzigen Query statt mehreren N+1 Queries
        $stockValues = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['buy', 'sell'])
            ->with(['stock:id,name']) // Eager Loading
            ->get()
            ->groupBy('stock_id')
            ->map(function ($transactions) {
                $totalQuantity = 0;
                foreach ($transactions as $t) {
                    $totalQuantity += ($t->type === 'buy' ? 1 : -1) * $t->quantity;
                }

                if ($totalQuantity > 0) {
                    $currentPrice = $transactions->first()->stock->getCurrentPrice() ?? 0;
                    return $totalQuantity * $currentPrice;
                }

                return 0;
            })
            ->sum();

        return $stockValues + $bankBalance;
    }

    /**
     * Depotwert basierend auf gespeicherten Kaufpreisen (nur aktuelle Holdings)
     */
    public function getTotalDepotValue(): float
    {
        $user = Auth::user();

        $buyValue = $user->transactions
            ->where('type', 'buy')
            ->reduce(fn($carry, $t) => $carry + ($t->quantity * ($t->resolvedPriceAtBuy() ?? 0)), 0);

        $sellValue = $user->transactions
            ->where('type', 'sell')
            ->reduce(fn($carry, $t) => $carry + ($t->quantity * ($t->resolvedPriceAtBuy() ?? 0)), 0);

        return $buyValue - $sellValue;
    }

    /**
     * Berechnet den Preis einer Transaktion
     */
    public function getPriceAtBuyForTransaction($transaction): float
    {
        // Use resolvedPriceAtBuy which handles stored value and fallbacks
        return (float) ($transaction->resolvedPriceAtBuy() ?? $transaction->stock?->getCurrentPrice() ?? 0);
    }

    /**
     * Durchschnittlicher Kaufpreis
     */
    public function calculateAverageBuyPrice($transactions, $beforeDate = null): float
    {
        if (empty($transactions)) {
            return 0;
        }

        $filtered = collect($transactions);

        if ($beforeDate) {
            // Datum auf Vormonat setzen
            $beforeDate = Carbon::parse($beforeDate)->startOfMonth()->subDay(); // letztes Datum des Vormonats
            $filtered = $filtered->filter(function ($t) use ($beforeDate) {
                $transactionDate = $t->gameTime?->name;
                return $transactionDate && Carbon::parse($transactionDate)->lte($beforeDate);
            });
        }

        $totalBought = $filtered->count();
        if ($totalBought <= 0) {
            return 0;
        }

        return $filtered->sum('price_at_buy') / $totalBought;
    }

    /**
     * Aggregierte Kennzahlen pro Aktie
     */
    public function getStockStatistiks( $transactions, $user, int $currentMonth = null)
    {
        if ($transactions instanceof Collection && $transactions->first() instanceof Stock) {
            $transactions = $transactions->all();
        } elseif ($transactions instanceof Stock) {
            $stock = $transactions;
            $transactions = $this->getUserBuyTransactionsForStock($user, $stock->id)->all();
        } else {
            if (empty($transactions) || !isset($transactions[0])) {
                throw new \InvalidArgumentException('No transactions provided for stock statistics calculation');
            }
            $stock = $transactions[0]->stock;
            $transactions = $transactions->all();
        }

        $totalQuantity = $stock->getCurrentQuantity($user); // Use consistent quantity calculation
        $buyPrice = $this->calculateAverageBuyPrice($transactions);
        $firstBuyDate = $stock->getFirstBuyTransactionDateForStock();
        $lastBuyDate = max(array_map(fn($t) => $t->getLastBuyTransactionDate(), $transactions));
        $currentPrice = $stock->getCurrentPrice();
        $profitLoss = ($currentPrice - $buyPrice) * $totalQuantity; // Fix profit calculation
        $profitLossPercent = $buyPrice > 0 ? ($currentPrice - $buyPrice) / $buyPrice * 100 : 0;
        $depositShareInPercent = $this->getDepositShareInPercent($user, $stock);

        $dividendService = new DividendeService();
        $dividends = $dividendService->getDividendStatisticsForStock($stock, $user);

        return (object) [
            'stock' => $stock,
            'current_price' => $currentPrice,
            'avg_buy_price' => $buyPrice,
            'quantity' => $totalQuantity,
            'last_buy' => Carbon::parse($lastBuyDate)->format('d.m.Y'),
            'first_buy' => Carbon::parse($firstBuyDate)->format('d.m.Y'),
            'profit_loss' => $profitLoss,
            'profit_loss_percent' => $profitLossPercent,
            'deposit_share_in_percent' => $depositShareInPercent,
            'dividende' => $dividends,
        ];
    }

    /**
     * Alle Aktien eines Users - Optimierte Version mit Eager Loading
     */
    public function getUserStocks($user)
    {
        return Cache::remember("user_stocks_{$user->id}", 300, function () use ($user) {
            // Eager Loading der Stock-Daten mit nur benötigten Feldern
            $stockIds = $user->transactions
                ->where('type', 'buy')
                ->pluck('stock_id')
                ->unique()
                ->filter();

            return Stock::whereIn('id', $stockIds)
                ->select('id', 'name', 'firma', 'sektor', 'land') // Nur benötigte Felder
                ->with(['prices' => function ($query) {
                    $query->select('id', 'stock_id', 'name', 'game_time_id')
                          ->orderBy('game_time_id', 'desc')
                          ->take(1); // Nur der neueste Preis
                }])
                ->get();
        });
    }

    /**
     * Alle Aktien mit Statistiken
     */
    public function getUserStocksWithStatistiks($user = null, int $currentMonth = null)
    {
        $user = $user ?? Auth::user();

        return Cache::remember("user_stocks_stats_{$user->id}", 300, function () use ($user, $currentMonth) {
            return $this->getUserStocks($user)
                ->filter(fn($stock) => $stock->getCurrentQuantity($user) > 0) // Nur Aktien mit positiver Menge einbeziehen
                ->map(fn($stock) => $this->getStockStatistiks($stock, $user, $currentMonth))
                ->values();
        });
    }

    /**
     * Buy-Transaktionen für eine Aktie
     */
    public function getUserBuyTransactionsForStock($user, $stockID)
    {
        return $user->transactions
            ->where('type', 'buy')
            ->where('stock_id', $stockID)
            ->sortBy('created_at')
            ->values();
    }

    /**
     * Anteil der Aktie am Depot basierend auf aktuellem Wert (nicht investiertem Kapital)
     */
    public function getDepositShareInPercent($user, $stock)
    {
        // Aktueller Wert dieser Aktie im Depot
        $currentStockValue = $stock->getCurrentQuantity($user) * $stock->getCurrentPrice();

        // Gesamtwert aller Aktien im Depot (nur Aktien mit positiver Menge)
        $totalPortfolioValue = $this->getUserStocks($user)
            ->filter(fn($s) => $s->getCurrentQuantity($user) > 0)
            ->sum(function ($s) use ($user) {
                return $s->getCurrentQuantity($user) * $s->getCurrentPrice();
            });

        if ($totalPortfolioValue == 0) {
            return 0; // Vermeidung von Division durch Null
        }

        return number_format((float) ($currentStockValue / $totalPortfolioValue) * 100, 2, '.', '');
    }

}
