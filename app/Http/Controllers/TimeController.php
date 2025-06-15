<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Stock\Price;
use App\Models\Stock\Stock;

class TimeController extends Controller
{
    /**
     * Array mit Monatsnamen für die Zeitsimulation.
     *
     * @var array
     */
    public $monthArray =  ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    /**
     * Zeigt die Monatsauswahl und den aktuellen Monat an.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $selectedMonth = session('selectedMonth', 'None');
        return view('time.index', ['monthArray' => $this->monthArray, 'selectedMonth' => $selectedMonth]);
    }

    /**
     * Setzt den gewählten Monat in der Session und ruft die Zeit-Sprung-Logik auf.
     *
     * @param \Illuminate\Http\Request $request HTTP-Request mit 'choose' als Parameter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $selectedMonth = $request->input('choose');
        $this->skipTime($selectedMonth);
        session(['selectedMonth' => $selectedMonth]);
        return redirect()->route('time.index');
    }

    /**
     * Führt die Logik für den Monatswechsel aus (z.B. neue Preise generieren).
     *
     * @param string $currentMonth Name des aktuellen Monats
     * @return void
     */
    public function skipTime($selectedMonth)
    {        
        $stocks = Stock::all();

        //wichtig $stock->price()->get()->last()->month!!!
        foreach ($stocks as $stock) {
            
            $lastDate = new \DateTime($stock->price()->get()->last()->date);

            $selectedMonth = date("m", strtotime($selectedMonth));
            $monthDifference = (12 + (int)$selectedMonth - (int)$lastDate->format("m")) % 12;

            if ($monthDifference == 0)             
                $monthDifference = 12;
            
            $lastPrice = $stock->price()->get()->last()->name;
            
            for ($i = 0; $i < $monthDifference; $i++) 
            {
                // Klonen, um das Original zu behalten (optional)
                $nextDate = clone $lastDate;
                $nextDate->modify('+1 month'); // korrektes Vorwärtsrechnen
            
                // Datum als String speichern
                $lastDateString = $nextDate->format('Y-m-d');
                
                $newPrice = round($lastPrice * (1 + rand(-25, 25) / 100), 2);
                $lastPrice = $newPrice;
                
                
                //dd([
                //    'Stock'     => $stock->name,
                //    'LastDate'  => $lastDate->format('Y-m-d'),
                //    'NextDate'  => $lastDateString,
                //    'Datediff' => $monthDifference,
                //    'LastPrice' => $lastPrice,
                //    'NewPrice'  => $newPrice
                //]);
                
                // Setze das Objekt für den neuen Preis aus dem clone                
                
                // Generiere einen neuen Preis für den Stock
                Price::create([
                    'stock_id' => $stock->id,
                    'date' => $nextDate,
                    'name' => $newPrice,
                ]);
                
                $lastDate = $nextDate;
                
            }            
        }        
    }     
}

