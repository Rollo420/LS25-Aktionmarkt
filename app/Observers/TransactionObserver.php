<?php

namespace App\Observers;

use App\Models\Stock\Transaction as StockTransaction;
use App\Models\Transaction;

class TransactionObserver
{
    public function creating(StockTransaction $transaction)
    {
        $transaction->user_id = app('actorId');
    }
}
