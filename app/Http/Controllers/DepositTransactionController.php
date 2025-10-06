<?php

namespace App\Models;

use Parental\HasParent;
use App\Models\Stock\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock\Stock;

class DepositTransactionController extends Transaction
{
    use HasParent;

    public function depot()
    {
        $user = Auth::user();

        $ownStocks = $user->transactions;

        

        // Logik zum Anzeigen des Depots
        return view('depot');
    }    
}
