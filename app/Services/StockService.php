<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

use App\Models\Stock\Stock;
use App\Models\Stock\Transaction;
use App\Models\BuyTransaction;

class StockService
{
    /**
     * Gesamtwert des Depots inkl. Bankguthaben
     */
    public function getTotalPortfolioValue(): float
    {
        $user = Auth::user();
        $bankBalance = $user->bank?->balance ?? 0;

        $transactions = Transaction::where('user_id', $user->id)->get();

        $totalStocksValue = $transactions->map(function ($transaction) {
            $lastPrice = $transaction->stock?->prices->last()?->name ?? 0;
            return $transaction->quantity * $lastPrice;
        })->sum();

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
            ->reduce(function ($carry, $t) {
                return $carry + ($t->quantity * ($t->price_at_buy ?? 0));
            }, 0);
    }

    /**
     * Aggregierte Kennzahlen pro Aktie
     */
    public function getStockStatistiks($transactions, $user)
    {
        // Typen abfragen, um Collection oder Stock zu standardisieren
        if ($transactions instanceof Collection && $transactions->first() instanceof Stock) {
            $transactions = $transactions->all();
        } elseif ($transactions instanceof Stock) {
            $stock = $transactions;
            $transactions = $this->getUserBuyTransactionsForStock($user, $stock->id);
        } else {
            $stock = $transactions->first()->stock;
        }

        // Gesamtmenge der gekauften Aktien
        $totalQuantity = $transactions->sum('quantity');

        // Gesamtwert aller Käufe basierend auf price_at_buy
        $totalCost = $transactions->reduce(fn($carry, $t) => $carry + ($t->price_at_buy ?? 0) * $t->quantity, 0);

        // Durchschnittlicher Kaufpreis
        $avgBuyPrice = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;

        // Letztes Kaufdatum
        $lastBuyDate = $transactions->max('created_at');

        // Aktueller Aktienpreis
        $currentPrice = $stock->getCurrentPrice();

        // Gewinn/Verlust (€ und %) – kein direktes Minus nach Kauf
        $profitLoss = $this->calculateProfitLoss($transactions);

        // Anteil der Aktie am Depot
        $depositShareInPercent = BuyTransaction::getDepositShareInPercent($user->id, $stock->id);

        // Dummy Dividenden-Daten
        $dividends = (object) [
            'next_date' => '15.12.2025',
            'next_amount' => 0.85,
            'frequency_per_year' => 4,
            'last_date' => '15.09.2025',
            'last_amount' => 0.85,
            'total_received' => 3456,
            'expected_next_12m' => 3680,
            'yield_percent' => 1.8,
        ];

        return (object) [
            'stock' => $stock,
            'current_price' => $currentPrice,
            'avg_buy_price' => $avgBuyPrice,
            'quantity' => $totalQuantity,
            'bought_at' => $lastBuyDate,
            'profit_loss' => $profitLoss,
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
            ->map(fn($group) => $group->first()->stock)
            ->values();
    }

    /**
     * Alle Aktien mit Statistiken
     */
    public function getUserStocksWithStatistiks($user = null)
    {
        $user = $user ?? Auth::user();

        return $this->getUserStocks($user)
            ->map(fn($stock) => $this->getStockStatistiks($stock, $user))
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
            ->sortBy('created_at');
    }

    /**
     * Gewinn/Verlust berechnen – modular, kein direktes Minus nach Kauf
     */
    public function calculateProfitLoss($transactions)
    {
        $totalQuantity = $transactions->sum('quantity');

        // Gesamtkosten basierend auf price_at_buy
        $totalCost = $transactions->reduce(fn($carry, $t) => $carry + ($t->quantity * ($t->price_at_buy ?? 0)), 0);

        // Aktueller Kurs
        $currentPrice = $transactions->first()->stock->getCurrentPrice();

        // Gewinn/Verlust: min 0 nach Kauf
        $profitLossAmount = max(0, $totalQuantity * ($currentPrice - ($totalQuantity > 0 ? $totalCost / $totalQuantity : 0)));
        $profitLossPercent = $totalCost > 0 ? ($profitLossAmount / $totalCost) * 100 : 0;

        return [
            'amount' => round($profitLossAmount, 2),
            'percent' => round($profitLossPercent, 1),
        ];
    }
}
