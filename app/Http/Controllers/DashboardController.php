<?php

namespace App\Http\Controllers;

use App\Models\Stock\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use \App\Models\Stock\Stock;


class DashboardController extends Controller
{

    public function index()
    {
        $depotInfo = [];

        $user = Auth::user();

        $depotInfo['totalPortfolioValue'] = $this->getTotalPortfolioValue();

        return view('dashboard', compact('depotInfo'));
    }

    private function getTotalPortfolioValue() : float
    {        
        $user = Auth::user();
        $bankBalance = $user->bank->balance;
       
        $transactions = Transaction::where('user_id', $user->id)->get();

        $allPrices = $transactions->map(function($transaction){

            $lastPriceName = $transaction->stock?->prices->last()?->name;
            $pricePerStock = $transaction->quantity * $lastPriceName;
            
            
            return $pricePerStock;
        });

        $totalPrice = 0;
        foreach($allPrices as $price) 
        {
            $totalPrice += $price;
        }
        
        //dd($totalPrice);

        return $totalPrice + $bankBalance;
    }

}
