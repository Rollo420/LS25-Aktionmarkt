<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockService;

use Illuminate\Support\Facades\Auth;
use App\Models\Stock\Stock;
use App\Models\BuyTransaction;
use App\Models\Stock\Transaction;

class DepositTransactionController extends Controller
{
    /**
     * Zeigt eine Übersicht aller Aktien des angemeldeten Users im Depot.
     *
     * @return \Illuminate\View\View
     */
    public function index(StockService $stockService)
    {
        // Aktuellen User holen
        $user = Auth::user();

        // Alle Aktien des Users mit aggregierten Kennzahlen holen
        $stocks = $stockService->getUserStocksWithStatistiks($user);

        #dd($stocks);
        // Zur Depot-Übersichtsseite weiterleiten
        return view('depot.index', compact('stocks'));
    }

    /**
     * Zeigt die Detailseite für eine bestimmte Aktie.
     * Enthält aggregierte Kennzahlen, Dividendeninfos und die letzten Käufe.
     *
     * @param int $id Stock-ID
     * @return \Illuminate\View\View
     */
    public function depotStockDetails($id, StockService $stockService)
    {
        $user = Auth::user();

        // Aktie anhand der ID laden
        $stock = Stock::findOrFail($id);
        
        // Alle Transaktionen des Users für diese Aktie
        $stockTransactionsHistory = $this->getUserStockTransactions($id)->get();

        // Aggregierte Kennzahlen berechnen
        $stockData = $stockService->getStockStatistiks($stockTransactionsHistory, $user);
        #dd($stockData);
        // View mit allen Daten zurückgeben
        return view('depot.depotStockDetails', compact('stock', 'stockData', 'stockTransactionsHistory'));
    }

    public function getUserStockTransactions($stockId)
    {
        $user = Auth::user();

        return Transaction::whereUserId($user->id)->whereStockId($stockId);

    }






}
