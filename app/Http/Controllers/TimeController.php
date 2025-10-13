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
    public $monthArray = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

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

        
        foreach ($stocks as $stock) {

            $lastDate = new \DateTime($stock->prices()->get()->last()->date);

            $selectedMonth = date("m", strtotime($selectedMonth));
            $monthDifference = (12 + (int) $selectedMonth - (int) $lastDate->format("m")) % 12;

            if ($monthDifference == 0)
                $monthDifference = 12;

            $lastPrice = $stock->prices()->get()->last()->name;
            $volatility = 0.10; // max. 5% Schwankung pro Monat (Faktor zum Spielen)

            for ($i = 0; $i < $monthDifference; $i++) {
                // Neues Datum (1 Monat weiter)
                $nextDate = (clone $lastDate)->modify('+1 month');
                $lastDateString = $nextDate->format('Y-m-d');

                // Zufällige prozentuale Änderung: z. B. zwischen -5% und +5%
                $changePercent = (random_int(-100, 250) / 100) * $volatility;
                // z. B. -0.034 = -3.4%, oder 0.048 = +4.8%

                // Neuen Kurs berechnen
                $newPrice = round($lastPrice * (1 + $changePercent), 2);

                // Sicherstellen, dass der Kurs nicht 0 oder negativ wird
                if ($newPrice <= 0) {
                    $newPrice = round($lastPrice * (1 + abs($changePercent)), 2);
                }

                // Optional: Zusätzlichen Random-Faktor (Markt-Ereignis-Simulation)
                if (random_int(1, 20) === 1) { // 1 von 20 Monaten Crash oder Hype
                    $eventFactor = random_int(-20, 20) / 100; // -20% bis +20%
                    $newPrice = round($newPrice * (1 + $eventFactor), 2);
                }

                // Speichern / weiterverwenden
                $lastPrice = $newPrice;

                // Debug-Ausgabe
                // echo "$lastDateString => $newPrice €\n";




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

