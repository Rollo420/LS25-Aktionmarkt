<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Stock\Stock;
use App\Models\Stock\Transaction;
use Illuminate\View\View;

use App\Services\DividendeService;



class StockController extends Controller
{
    /**
     * Listet alle Aktien mit ihrem aktuellen Preis auf.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        
        $allStocks = Stock::all();

        $stocks[] = $allStocks->map(function ($stock)
        {
            return [
                    'id' => $stock->id,
                    'name' => $stock->name,
                    'price' => $stock->getCurrentPrice()
            ];
            
        });
        
        
        $stocks = collect($stocks);
        
        return view('Stock.index', ['stocks' => $stocks->first()]);
    }

    /**
     * Berechnet und gibt die wichtigsten Kennzahlen einer Aktie zurück.
     *
     * @param int $id Die ID der Aktie
     * @return array Details wie aktueller Preis, Kursentwicklung, EPS, Dividendenrendite, KGV
     */
    public function stockDetails(int $id)
    {
        $details = [];

        $stock = Stock::with('prices')->findOrFail($id); // Eager Loading für Preise
        
        //current price
        $prices = $stock->prices; // Get all prices
        $details['currentPrice'] = $stock->getCurrentPrice(); // Last price
        //dd($details['currentPrice']);

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
        $details['payoutRatio'] = ($details['eps'] > 0 && $details['currentPrice'] > 0)
            ? ($details['eps'] / $details['currentPrice']) * 100
            : 0;

        //kgv
        $details['kgv'] = ($details['eps'] > 0)
            ? $details['currentPrice'] / $details['eps']
            : 0;

        //hier fehlt die dividende
        $dividendeService = new DividendeService();
        $dividendDetails = $dividendeService->getDividendeForStock($id);
        $details = array_merge($details, $dividendDetails);

        //dd( $details);

        return $details;
    }

}
