<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock\Stock;
use App\Models\BuyTransaction;

class DepositTransactionController extends Controller
{
    /**
     * Zeigt eine Übersicht aller Aktien des angemeldeten Users im Depot.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Aktuellen User holen
        $user = Auth::user();

        // Alle Aktien des Users mit aggregierten Kennzahlen holen
        $stocks = $this->getUserStocks($user);

        // Zur Depot-Übersichtsseite weiterleiten
        return view('depot.index', compact('stocks'));
    }

    /**
     * Zeigt die Detailseite für eine bestimmte Aktie.
     * Enthält aggregierte Kennzahlen, Dividendeninfos und die letzten Käufe.
     *
     * @param int $id Stock-ID
     * @return \Illuminate\View\View
     */
    public function depotStockDetails($id)
    {
        $user = Auth::user();

        // Aktie anhand der ID laden
        $stock = Stock::findOrFail($id);

        // Alle Buy-Transaktionen des Users für diese Aktie
        $stockBuyTransactions = $user->transactions
            ->where('type', 'buy')
            ->where('stock_id', $id)
            ->sortBy('created_at');

        // Aggregierte Kennzahlen berechnen
        $stockData = $this->mapTransactionsToStock($stockBuyTransactions, $user);

        // Letzte 3 Käufe für die Kaufhistorie
        $stockBuyHistory = $stockBuyTransactions->take(-3);

        // View mit allen Daten zurückgeben
        return view('depot.depotStockDetails', compact('stock', 'stockData', 'stockBuyHistory'));
    }

    /**
     * Holt alle Aktien eines Users mit aggregierten Kennzahlen.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Support\Collection
     */
    private function getUserStocks($user)
    {
        // Alle Buy-Transaktionen des Users
        $buyTransactions = $user->transactions->where('type', 'buy');

        // Transaktionen nach Aktie gruppieren und Kennzahlen berechnen
        return $buyTransactions->groupBy('stock_id')->map(function ($transactions) use ($user) {
            return $this->mapTransactionsToStock($transactions, $user);
        })->values();
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
    private function mapTransactionsToStock($transactions, $user)
    {
        $stock = $transactions->first()->stock;

        // Gesamtmenge aller gekauften Aktien
        $totalQuantity = $transactions->sum('quantity');

        // Gesamtkosten aller Käufe berechnen
        $totalCost = $transactions->reduce(function ($carry, $t) {
            $priceAtBuy = $t->stock->prices()
                ->where('created_at', '<=', $t->created_at)
                ->latest('created_at')
                ->first()
                ->name ?? 0;
            return $carry + ($t->quantity * $priceAtBuy);
        }, 0);

        // Durchschnittlicher Kaufpreis
        $avgBuyPrice = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;

        // Letztes Kaufdatum
        $lastBuyDate = $transactions->max('created_at');

        // Aktueller Aktienpreis
        $currentPrice = $stock->prices()->latest('created_at')->first()->name ?? 0;

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
            'id' => $stock->id,
            'name' => $stock->name,
            'current_price' => $currentPrice,
            'avg_buy_price' => $avgBuyPrice,
            'quantity' => $totalQuantity,
            'bought_at' => $lastBuyDate,
            'profit_loss' => $profitLoss,
            'deposit_share_in_percent' => $depositShareInPercent,
            'dividends' => $dividends,
        ];
    }
}
