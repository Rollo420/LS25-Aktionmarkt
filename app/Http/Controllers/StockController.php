<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock\Stock;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index()
    {
        $stockWithPrice = [];
        $stocks = Stock::all();
        
        foreach ($stocks as $stock)
        {
            $lastPrice = $stock->price->last();
            $priceName = $lastPrice ? $lastPrice->name : 'No Price';
            array_push($stockWithPrice, [$stock->id, $stock->name, $priceName]);
        }
        // dd($stockWithPrice);
        return view('Stock.index', ['stocks' => $stockWithPrice]);
    }

    public function details(int $id)
    {
        $details = [];
        $currentPrice = 0.0;
        $priceChange = 0.0;
        $dividendDistribution = 0.0;
        $percentageDevelopment = 0.0; 
        $eps = 0.0; // Earnings Per Share

        $stock = Stock::findOrFail($id);

        $prices = $stock->price; // Get all prices
        $details['currentPrice'] = $prices->last()->name; // Last price

        $previousPrice = $prices->slice(-2, 1)->first(); // Second-to-last price
        $details['priceChange'] = $previousPrice ? $details['currentPrice'] - $previousPrice->name : 0;



        //dd($details);

        return $details;
    }

}
