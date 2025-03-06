<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock\Stock;
use Illuminate\View\View;
class StockController extends Controller
{
    public function index()
    {
        //$stocks = Stock::all();
        //$stockPrice = Stock::find(1)->price;
        $stocks = Stock::with('transactions')->get();
        return view('Stock.index', ['stocks' => $stocks]);
    }

    
}
