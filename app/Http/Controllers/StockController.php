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
        $allStocks = Stock::with(['dividends' => function ($query) {
            $query->with('gameTime')->orderBy('game_time_id', 'desc');
        }])->get();

        $stocks = $allStocks->map(function ($stock) {
            $dividendeService = new DividendeService();
            $dividendData = $dividendeService->getDividendeForStockID($stock->id);

            return [
                'id' => $stock->id,
                'name' => $stock->name,
                'firma' => $stock->firma,
                'sektor' => $stock->sektor,
                'land' => $stock->land,
                'price' => $stock->getCurrentPrice(),
                'dividend_amount' => $dividendData ? $dividendData->next_amount : null,
                'next_dividend_date' => $dividendData ? $dividendData->next_date : null,
            ];
        });

        return view('Stock.index', ['stocks' => $stocks]);
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

        $stock = Stock::with(['prices' => function ($query) {
            $query->orderBy('game_time_id', 'desc');
        }])->findOrFail($id); // Eager Loading für Preise, ordered by game_time_id desc

        //current price
        $prices = $stock->prices; // Get all prices, now ordered by game_time_id desc
        $details['currentPrice'] = $stock->getCurrentPrice(); // Last price
        //dd($details['currentPrice']);

        //price change
        $previousPrice = $prices->skip(1)->first(); // Second price in the ordered list (previous month)
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


        $dividendeService = new DividendeService();
        $details['dividende'] = $dividendeService->getDividendeForStockID($id);


        //dd( $details);

        return $details;
    }

}
