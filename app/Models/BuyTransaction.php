<?php

namespace App\Models;

use Parental\HasParent;
use App\Models\Stock\Transaction;

class BuyTransaction extends Transaction
{
    use HasParent;

    public static function getDepositShareInPercent($userId, $stockId)
    {
        $user = User::find($userId);
        if (!$user) {
            return 0; // Benutzer nicht gefunden
        }

        $buyTransactions = $user->transactions()
            ->where('type', 'buy')
            ->where('stock_id', $stockId);

        $totalValue = $buyTransactions->get()->sum(function ($transaction) {
            return $transaction->quantity * ($transaction->price_at_buy ?? 0);
        });

        $totalInvested = $user->transactions()
            ->where('type', 'buy')
            ->get() // ->get() macht eine Collection
            ->sum(function ($transaction) {
                return $transaction->quantity * ($transaction->price_at_buy ?? 0);
            });


        if ($totalInvested == 0) {
            return 0; // Vermeidung von Division durch Null
        }

        return number_format((float) ($totalValue / $totalInvested) * 100, 2, '.', '');

    }

    /**
     * Preis pro Kauf für eine Transaktion ermitteln (statische Version)
     */
    private static function getPriceAtBuyForTransaction($transaction): float
    {
        // 1) Direkt gespeicherter Preis bei Kauf
        if ($transaction->price_at_buy > 0) {
            return $transaction->price_at_buy;
        }

        // 2) Historischer Preis zum Zeitpunkt der Transaktion
        $priceObj = $transaction->stock->prices()
            ->where('date', '<=', $transaction->created_at)
            ->latest('date')
            ->first();
        if ($priceObj) {
            return $priceObj->name;
        }

        // 3) Fallback auf aktuellen Preis
        return $transaction->stock->getCurrentPrice();
    }
}
