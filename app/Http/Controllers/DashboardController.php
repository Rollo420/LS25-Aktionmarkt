<?php

namespace App\Http\Controllers;

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

        $depotInfo['totalPortfolio'] = $this->getTotalPortfolioValue();

        return view('dashboard', compact('depotInfo'));
    }

    private function getTotalPortfolioValue() : float
    {        
        $user = Auth::user();
        $bankBalance = $user->bank->balance;
        //$results = Stock->map(function($stock) {
        //    return $stock->price * $stock->transaction->quantity;
        //});
        dd($bankBalance);

    return 1.12;
    }
}
