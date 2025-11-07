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
            return $transaction->quantity * ($transaction->resolvedPriceAtBuy() ?? 0);
        });

        $totalInvested = $user->transactions()
            ->where('type', 'buy')
            ->get()
            ->sum(function ($transaction) {
                return $transaction->quantity * ($transaction->resolvedPriceAtBuy() ?? 0);
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
        return (float) ($transaction->resolvedPriceAtBuy() ?? $transaction->stock?->getCurrentPrice() ?? 0);
    }


}
