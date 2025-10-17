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

        $totalStocksValue = $user->transactions
            ->where('type', 'buy')
            ->map(function ($transaction) {
                $lastPrice = $transaction->stock?->getCurrentPrice() ?? 0;
                return $transaction->quantity * $lastPrice;
            })
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
            ->reduce(function ($carry, $t) {
                $price = $t->price_at_buy ?? $t->stock?->getCurrentPrice() ?? 0;
                return $carry + ($t->quantity * $price);
            }, 0);
    }

    /**
     * Durchschnittlicher Kaufpreis für eine Aktie berechnen
     */
    public function calculateAverageBuyPrice($transactions): float
    {
        $totalQuantity = $transactions->sum('quantity');
        if ($totalQuantity <= 0)
            return 0;

        $totalCost = 0;
        foreach ($transactions as $t) {
            // 1) Preis bei Kauf aus der Transaktion
            if ($t->price_at_buy > 0) {
                $price = $t->price_at_buy;
            } else {
                // 2) Historischen Preis ermitteln, fallback auf aktuellen Preis
                $priceObj = $t->stock->prices()
                    ->where('date', '<=', $t->created_at)
                    ->latest('date')
                    ->first();
                $price = $priceObj?->name ?? $t->stock->getCurrentPrice();
            }
            $totalCost += $price * $t->quantity;
        }

        return $totalCost / $totalQuantity;
    }


    /**
     * Gewinn/Verlust berechnen – keine sofortigen Verluste nach Kauf
     */
    public function calculateProfitLoss($transactions): array
    {
        $totalQuantity = $transactions->sum('quantity');
        if ($totalQuantity <= 0)
            return ['amount' => 0, 'percent' => 0];

        $avgBuyPrice = $this->calculateAverageBuyPrice($transactions);
        $currentPrice = $transactions->first()->stock->getCurrentPrice();

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
    public function getStockStatistiks($transactions, $user)
    {
        // Standardisiere Transaktionen auf Collection pro Aktie
        if ($transactions instanceof Collection && $transactions->first() instanceof Stock) {
            $transactions = $transactions->all();
        } elseif ($transactions instanceof Stock) {
            $stock = $transactions;
            $transactions = $this->getUserBuyTransactionsForStock($user, $stock->id);
        } else {
            $stock = $transactions->first()->stock;
        }

        $totalQuantity = $transactions->sum('quantity');
        $avgBuyPrice = $this->calculateAverageBuyPrice($transactions);
        $lastBuyDate = $transactions->max('created_at');
        $currentPrice = $stock->getCurrentPrice();
        $profitLoss = $this->calculateProfitLoss($transactions);
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
}
