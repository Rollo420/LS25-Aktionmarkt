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
            ->map(fn($t) => $t->quantity * $t->stock->getCurrentPrice())
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
                $price = $this->getPriceAtBuyForTransaction($t);
                return $carry + ($t->quantity * $price);
            }, 0);
    }

    /**
     * Durchschnittlicher Kaufpreis für eine Aktie berechnen
     */
    public function calculateAverageBuyPrice($transactions): float
    {
        $transactions = collect($transactions);
        $totalQuantity = $transactions->sum('quantity');
        if ($totalQuantity <= 0)
            return 0;

        $totalCost = $transactions->reduce(function ($carry, $t) {
            $price = $this->getPriceAtBuyForTransaction($t);
            return $carry + ($price * $t->quantity);
        }, 0);

        return $totalCost / $totalQuantity;
    }

    /**
     * Preis pro Kauf für eine Transaktion ermitteln
     */
    private function getPriceAtBuyForTransaction($transaction): float
    {
        if ($transaction->price_at_buy > 0)
            return $transaction->price_at_buy;

        $priceObj = $transaction->stock->prices()
            ->where('date', '<=', $transaction->created_at)
            ->latest('date')
            ->first();

        return $priceObj?->name ?? $transaction->stock->getCurrentPrice();
    }

    /**
     * Gewinn/Verlust berechnen
     * - nur für Kursbewegungen nach Kaufzeitpunkt
     * - neue Käufe zum aktuellen Preis liefern noch 0
     */
    public function calculateProfitLoss($transactions): array
    {
        $transactions = collect($transactions);
        if ($transactions->isEmpty())
            return ['amount' => 0, 'percent' => 0];

        $stock = $transactions->first()->stock;
        $profitLossAmount = 0;
        $totalQuantity = 0;

        $currentPrice = $stock->getCurrentPrice();

        foreach ($transactions as $t) {
            $buyPrice = $this->getPriceAtBuyForTransaction($t);

            // Wenn die Transaktion heute oder später als letzter Kurs, noch kein Profit/Loss
            if ($t->created_at->gte(now())) {
                continue;
            }

            $profitLossAmount += ($currentPrice - $buyPrice) * $t->quantity;
            $totalQuantity += $t->quantity;
        }

        if ($totalQuantity <= 0)
            return ['amount' => 0, 'percent' => 0];

        $avgBuyPrice = $this->calculateAverageBuyPrice($transactions);
        $profitLossPercent = ($profitLossAmount / ($totalQuantity * $avgBuyPrice)) * 100;

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
        if ($transactions instanceof Collection && $transactions->first() instanceof Stock) {
            $transactions = $transactions->all();
        } elseif ($transactions instanceof Stock) {
            $stock = $transactions;
            $transactions = $this->getUserBuyTransactionsForStock($user, $stock->id);
        } else {
            $stock = $transactions->first()->stock;
        }

        $totalQuantity = collect($transactions)->sum('quantity');
        $avgBuyPrice = $this->calculateAverageBuyPrice($transactions);
        $lastBuyDate = collect($transactions)->max('created_at');
        $currentPrice = $stock->getCurrentPrice();
        $profitLoss = $this->calculateProfitLoss($transactions);
        $depositShareInPercent = BuyTransaction::getDepositShareInPercent($user->id, $stock->id);

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

    public function getUserStocks($user)
    {
        return $user->transactions
            ->where('type', 'buy')
            ->groupBy('stock_id')
            ->map(fn($group) => $group->first()->stock)
            ->values();
    }

    public function getUserStocksWithStatistiks($user = null)
    {
        $user = $user ?? Auth::user();

        return $this->getUserStocks($user)
            ->map(fn($stock) => $this->getStockStatistiks($stock, $user))
            ->values();
    }

    public function getUserBuyTransactionsForStock($user, $stockID)
    {
        return $user->transactions
            ->where('type', 'buy')
            ->where('stock_id', $stockID)
            ->sortBy('created_at');
    }
}
