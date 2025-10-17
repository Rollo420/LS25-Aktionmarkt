<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

use App\Models\Stock\Stock;
use App\Models\Stock\Transaction;
use App\Models\BuyTransaction;

use App\Services\DividendeService;

class StockService
{

    public function getTotalPortfolioValue(): float
    {
        $user = Auth::user();
        $bankBalance = $user->bank->balance;

        $transactions = Transaction::where('user_id', $user->id)->get();

        $allPrices = $transactions->map(function ($transaction) {

            $lastPriceName = $transaction->stock?->prices->last()?->name;
            $pricePerStock = $transaction->quantity * $lastPriceName;

            return $pricePerStock;
        });

        $totalPrice = 0;
        foreach ($allPrices as $price) {
            $totalPrice += $price;
        }

        //dd($totalPrice);

        return $totalPrice + $bankBalance;
    }

    public function getTotalDepotValue(): float
    {
        $user = Auth::user();

        $transactions = $user->transactions
            ->where('type', 'buy')
            ->sortBy('created_at');

        // Gesamtkosten aller Käufe berechnen
        $totalCost = $transactions->reduce(function ($carry, $t) {
            $priceAtBuy = $t->stock->prices()
                ->where('created_at', '<=', $t->created_at)
                ->latest('created_at')
                ->first()
                ->name ?? 0;
            return $carry + ($t->quantity * $priceAtBuy);
        }, 0);

        return $totalCost;
    }

    /**
     * Berechnet aggregierte Kennzahlen für eine Aktie basierend auf den Buy-Transaktionen.
     * Dazu zählen:
     * - Gesamtmenge
     * - Durchschnittlicher Kaufpreis
     * - Aktueller Preis
     * - Gewinn/Verlust
     * - Anteil im Depot
     * - Dummy-Dividendeninformationen
     *
     * @param \Illuminate\Support\Collection $transactions Buy-Transaktionen einer Aktie
     * @param \App\Models\User $user
     * @return object
     */
    public function getStockStatistiks($transactions, $user)
    {
        #dd($transactions);
        if ($transactions instanceof Collection && $transactions->first() instanceof Stock)
        {
            $transactions = $transactions->all();
            #dd($transactions);
        }
        else if ($transactions instanceof Stock) {
            $stock = $transactions;
            $transactions = $this->getUserBuyTransactionsForStock($user, $stock->id);
        }
        else if ($transactions instanceof Collection) {
            $stock = $transactions->first()->stock;
        }        
        
        
        // Gesamtmenge aller gekauften Aktien
        $totalQuantity = $transactions->sum('quantity');

        $totalCost = $this->getTotalDepotValue();

        // Durchschnittlicher Kaufpreis
        $avgBuyPrice = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;

        // Letztes Kaufdatum
        $lastBuyDate = $transactions->max('created_at');

        // Aktueller Aktienpreis
        $currentPrice = $stock->getCurrentPrice();

        // Gewinn / Verlust in € berechnen
        $profitLossAmount = $totalQuantity * ($currentPrice - $avgBuyPrice);

        // Gewinn / Verlust in % berechnen
        $profitLossPercent = $totalCost > 0 ? ($profitLossAmount / $totalCost) * 100 : 0;

        // Als Array speichern
        $profitLoss = [
            'amount' => round($profitLossAmount, 2),    // z.B. 18277.00
            'percent' => round($profitLossPercent, 1),  // z.B. 45.6
        ];

        // Anteil der Aktie am Depot in %
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
     * Holt alle Aktien eines Users mit aggregierten Kennzahlen.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Support\Collection
     */
    public function getUserStocks($user)
{
    return $user->transactions
        ->where('type', 'buy')
        ->groupBy('stock_id')
        ->map(fn($group) => $group->first()->stock)
        ->values(); // gibt Collection aus Stock-Objekten zurück
}


    public function getUserStocksWithStatistiks($user = null)
    {
        #$user = $user ?? Auth::user();

        #dd($this->getUserStocks($user));
        
        return $this->getUserStocks($user)
            ->map(function ($transactions) use ($user) {
                #dd($transactions->first());
                return $this->getStockStatistiks($transactions, $user);
            })
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