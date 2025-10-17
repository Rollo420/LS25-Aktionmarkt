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
            $price = $transaction->price_at_buy ?? 0;
            if ($price <= 0) {
                // Für alte Transaktionen ohne price_at_buy verwende aktuellen Preis
                $price = $transaction->stock->getCurrentPrice();
            }
            return $transaction->quantity * $price;
        });

        $totalInvested = $user->transactions()
            ->where('type', 'buy')
            ->get() // ->get() macht eine Collection
            ->sum(function ($transaction) {
                $price = $transaction->price_at_buy ?? 0;
                if ($price <= 0) {
                    // Für alte Transaktionen ohne price_at_buy verwende aktuellen Preis
                    $price = $transaction->stock->getCurrentPrice();
                }
                return $transaction->quantity * $price;
            });


        if ($totalInvested == 0) {
            return 0; // Vermeidung von Division durch Null
        }

        return number_format((float) ($totalValue / $totalInvested) * 100, 2, '.', '');

    }
}
