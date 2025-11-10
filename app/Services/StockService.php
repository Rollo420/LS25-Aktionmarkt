<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;  

use App\Services\DividendeService;

use App\Models\Stock\Stock;
use App\Models\Stock\Transaction;
use App\Models\BuyTransaction;

class StockService
{
    /**
     * Gesamtwert des Portfolios inkl. Bankguthaben
     */
    public function getTotalPortfolioValue(): float
    {
        $user = Auth::user();
        $bankBalance = $user->bank?->balance ?? 0;

        $totalStocksValue = $user->transactions
            ->where('type', 'buy')
            ->map(fn($t) => $t->quantity * ($t->stock->getCurrentPrice() ?? 0))
            ->sum();

        return $totalStocksValue + $bankBalance;
    }

    /**
     * Depotwert basierend auf gespeicherten Kaufpreisen
     */
    public function getTotalDepotValue(): float
    {
        $user = Auth::user();

        return $user->transactions
            ->where('type', 'buy')
            ->reduce(fn($carry, $t) => $carry + ($t->quantity * ($t->resolvedPriceAtBuy() ?? 0)), 0);
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
            $stock = $transactions[0]->stock;
            $transactions = $transactions->all();
        }

        $totalQuantity = array_sum(array_map(fn($t) => $t->quantity, $transactions));
        $buyPrice = $this->calculateAverageBuyPrice($transactions);
        $firstBuyDate = $stock->getFirstBuyTransactionDateForStock();
        $lastBuyDate = max(array_map(fn($t) => $t->getLastBuyTransactionDate(), $transactions));
        $currentPrice = $stock->getCurrentPrice();
        $profitLoss = ($currentPrice - $this->calculateAverageBuyPrice(collect($transactions)->first()->getLastBuyTransactionDate())) * $totalQuantity;
        $profitLossPercent = ($currentPrice - $buyPrice) / $buyPrice * 100;
        $depositShareInPercent = $this->getDepositShareInPercent($user, $stock);

        $dividendService = new DividendeService();
        $dividends = $dividendService->getDividendStatisticsForStock($stock);

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
     * Alle Aktien eines Users
     */
    public function getUserStocks($user)
    {
        return $user->transactions
            ->where('type', 'buy')
            ->groupBy('stock_id')
            ->map(fn($group) => $group[0]->stock)
            ->values();
    }

    /**
     * Alle Aktien mit Statistiken
     */
    public function getUserStocksWithStatistiks($user = null, int $currentMonth = null)
    {
        $user = $user ?? Auth::user();

        return $this->getUserStocks($user)
            ->filter(fn($stock) => $stock->getCurrentQuantity() > 0) // Nur Aktien mit positiver Menge einbeziehen
            ->map(fn($stock) => $this->getStockStatistiks($stock, $user, $currentMonth))
            ->values();
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
        $currentStockValue = $stock->getCurrentQuantity() * $stock->getCurrentPrice();

        // Gesamtwert aller Aktien im Depot (nur Aktien mit positiver Menge)
        $totalPortfolioValue = $this->getUserStocks($user)
            ->filter(fn($s) => $s->getCurrentQuantity() > 0)
            ->sum(function ($s) {
                return $s->getCurrentQuantity() * $s->getCurrentPrice();
            });

        if ($totalPortfolioValue == 0) {
            return 0; // Vermeidung von Division durch Null
        }

        return number_format((float) ($currentStockValue / $totalPortfolioValue) * 100, 2, '.', '');
    }

    /**
     * Alle Aktien eines Users mit aktuellen Holdings
     */
    public static function getUserStocksWithHoldings($user)
    {
        return $user->transactions
            ->where('type', 'buy')
            ->groupBy('stock_id')
            ->map(function($group) {
                $stock = $group[0]->stock;
                $quantity = $stock->getCurrentQuantity();
                return [
                    'stock' => $stock,
                    'quantity' => $quantity
                ];
            })
            ->filter(fn($item) => $item['quantity'] > 0)
            ->values()
            ->toArray();
    }

}
