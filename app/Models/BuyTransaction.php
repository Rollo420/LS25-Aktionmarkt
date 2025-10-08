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
            $latestPrice = $transaction->stock->prices()
                ->latest('created_at')
                ->first()
                ->name ?? 1;

            return $transaction->quantity * $latestPrice;
        });

        $totalInvested = $user->transactions()
            ->where('type', 'buy')
            ->get() // ->get() macht eine Collection
            ->sum(function ($transaction) {
                $latestPrice = $transaction->stock->prices()
                    ->latest('created_at')
                    ->first()
                    ->name ?? 1;

                return $transaction->quantity * $latestPrice;
            });


        if ($totalValue == 0) {
            return 0; // Vermeidung von Division durch Null
        }

        return number_format((float) ($totalValue / $totalInvested) * 100, 2, '.', '');

    }
}
