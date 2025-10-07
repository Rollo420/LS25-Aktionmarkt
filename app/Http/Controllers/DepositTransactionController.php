<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock\Stock;
use App\Http\Controllers\Controller;

class DepositTransactionController extends Controller
{
    public function depot()
    {
        $user = Auth::user();

        $buyTransactions = $user->transactions->where('type', 'buy');
        $stocks = $buyTransactions->groupBy('stock_id')->map(function ($transactions) {
            $stock = $transactions->first()->stock;

            $totalQuantity = 0;
            $totalCost = 0;

            foreach ($transactions as $t) {
                // Preis zum Zeitpunkt der Transaktion holen
                $priceAtBuy = $t->stock->prices()
                    ->where('created_at', '<=', $t->created_at)
                    ->latest('created_at')
                    ->first()
                    ->name ?? 0; // name = Preis

                $totalQuantity += $t->quantity;
                $totalCost += $t->quantity * $priceAtBuy;
            }

            $avgBuyPrice = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;

            $lastBuyDate = $transactions->max('created_at');

            // Aktueller Preis der Aktie
            $currentPrice = $stock->prices()->latest('created_at')->first()->name ?? 0;

            // Gewinn / Verlust
            $profitLoss = $totalQuantity * ($currentPrice - $avgBuyPrice);

            return (object) [
                'name' => $stock->name,
                'current_price' => $currentPrice,
                'avg_buy_price' => $avgBuyPrice,
                'quantity' => $totalQuantity,
                'bought_at' => $lastBuyDate,
                'profit_loss' => $profitLoss,
            ];
        })->values();


        return view('depot', compact('stocks'));
    }
}

/*
name der aktie
Kaufpreis
aktueller preis
menge
wann gekauft

*/
