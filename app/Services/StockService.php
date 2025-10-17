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
            ->reduce(fn($carry, $t) => $carry + ($t->quantity * $this->getPriceAtBuyForTransaction($t)), 0);
    }

    /**
     * Berechnet den Preis einer Transaktion
     */
    public function getPriceAtBuyForTransaction($transaction): float
    {
        if ($transaction->price_at_buy > 0) {
            return $transaction->price_at_buy;
        }

        // Fallback auf aktuellen Preis
        return $transaction->stock->getCurrentPrice();
    }

    /**
     * Durchschnittlicher Kaufpreis
     */
    public function calculateAverageBuyPrice($transactions): float
    {
        if (empty($transactions))
            return 0;

        $totalQuantity = array_sum(array_map(fn($t) => $t->quantity, $transactions));
        if ($totalQuantity <= 0)
            return 0;

        $totalCost = 0;
        foreach ($transactions as $t) {
            $totalCost += $this->getPriceAtBuyForTransaction($t) * $t->quantity;
        }

        return $totalCost / $totalQuantity;
    }

    /**
     * Gewinn/Verlust berechnen unter Berücksichtigung Ingame-Monate
     */
    public function calculateProfitLoss($transactions, int $currentMonth = null): array
    {
        if (empty($transactions))
            return ['amount' => 0, 'percent' => 0];

        $currentMonth = $currentMonth ?? max(array_map(fn($t) => $t->ingame_month ?? 1, $transactions));

        // Nur Transaktionen berücksichtigen, die vor dem aktuellen Monat gekauft wurden
        $filteredTransactions = array_filter($transactions, fn($t) => ($t->ingame_month ?? 0) < $currentMonth);

        if (empty($filteredTransactions))
            return ['amount' => 0, 'percent' => 0];

        $totalQuantity = array_sum(array_map(fn($t) => $t->quantity, $filteredTransactions));
        if ($totalQuantity <= 0)
            return ['amount' => 0, 'percent' => 0];

        $avgBuyPrice = $this->calculateAverageBuyPrice($filteredTransactions);
        $currentPrice = $filteredTransactions[0]->stock->getCurrentPrice();

        $totalCost = $avgBuyPrice * $totalQuantity;
        $currentValue = $currentPrice * $totalQuantity;

        $profitLossAmount = $currentValue - $totalCost;
        $profitLossPercent = $totalCost > 0 ? ($profitLossAmount / $totalCost) * 100 : 0;

        return [
            'amount' => round($profitLossAmount, 2),
            'percent' => round($profitLossPercent, 1),
        ];
    }

    /**
     * Aggregierte Kennzahlen pro Aktie
     */
    public function getStockStatistiks($transactions, $user, int $currentMonth = null)
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
        $avgBuyPrice = $this->calculateAverageBuyPrice($transactions);
        $lastBuyDate = max(array_map(fn($t) => $t->created_at, $transactions));
        $currentPrice = $stock->getCurrentPrice();
        $profitLoss = $this->calculateProfitLoss($transactions, $currentMonth);
        $depositShareInPercent = $this->getDepositShareInPercent($user, $stock);

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
     * Anteil der Aktie am Depot basierend auf investiertem Kapital
     */
    public function getDepositShareInPercent($user, $stock)
    {
        $buyTransactions = $user->transactions
            ->where('type', 'buy')
            ->where('stock_id', $stock->id);

        $totalValue = $buyTransactions->sum(function ($transaction) {
            return $transaction->quantity * ($transaction->price_at_buy ?? 0);
        });

        $totalInvested = $user->transactions
            ->where('type', 'buy')
            ->sum(function ($transaction) {
                return $transaction->quantity * ($transaction->price_at_buy ?? 0);
            });

        if ($totalInvested == 0) {
            return 0; // Vermeidung von Division durch Null
        }

        return number_format((float) ($totalValue / $totalInvested) * 100, 2, '.', '');
    }
}
