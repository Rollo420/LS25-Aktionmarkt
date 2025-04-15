<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock\Stock;
use App\Models\Stock\Transaction;
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

    public function firmenDetails(int $id)
    {
        $stock = Stock::findOrFail($id);

        

    }

    public function stockDetails(int $id)
    {
        $details = [];

        $stock = Stock::findOrFail($id);

        //current price
        $prices = $stock->price; // Get all prices
        $details['currentPrice'] = $prices->last()->name; // Last price

        //price change
        $previousPrice = $prices->slice(-2, 1)->first(); // Second-to-last price
        $details['priceDevelopment'] = $previousPrice ? $details['currentPrice'] - $previousPrice->name : 0;

        //percentage development
        $details['percentageDevelopment'] = ($previousPrice && $previousPrice->name != 0)
            ? (($details['currentPrice'] - $previousPrice->name) / $previousPrice->name) * 100
            : 0;

        //earnings per share
        $totalShares = Transaction::where('stock_id', $id)->sum('quantity') ?: 1; // Avoid division by 0
        $details['eps'] = $stock->net_income != 0
            ? $stock->net_income / $totalShares
            : 0;

        //dividend distribution
        $details['dividendDistribution'] = ($details['eps'] > 0 && $details['currentPrice'] > 0)
            ? ($details['eps'] / $details['currentPrice']) * 100
            : 0;

        //kgv
        $details['kgv'] = ($details['eps'] > 0)
            ? $details['currentPrice'] / $details['eps']
            : 0;

        return $details;
    }

}
