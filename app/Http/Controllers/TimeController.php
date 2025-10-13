<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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


            $expectedReturn = 0.16;   // 7 % Jahresrendite (langfristiger Trend)
            $volatility = 0.25;       // 15 % jährliche Volatilität
            $crashProbability = 1 / 240; // ca. alle 20 Jahre
            $rallyProbability = 1 / 240; // ebenfalls selten



            for ($i = 0; $i < $monthDifference; $i++) {
                // Neues Datum (1 Monat weiter)
                $nextDate = Carbon::parse($lastDate)->addMonth();

                // ======= Zufallswert aus Normalverteilung (Box-Muller) =======
                $u1 = mt_rand() / mt_getrandmax();
                $u2 = mt_rand() / mt_getrandmax();
                $z = sqrt(-2 * log($u1)) * cos(2 * M_PI * $u2);

                // ======= Monatliche Werte =======
                $monthlyVolatility = $volatility / sqrt(12);
                $monthlyReturn = $expectedReturn / 12;

                // ======= Kursentwicklung =======
                $changeFactor = exp(($monthlyReturn - 0.5 * pow($monthlyVolatility, 2)) + $monthlyVolatility * $z);
                $newPrice = round($lastPrice * $changeFactor, 2);

                // ======= Crashs & Rallyes =======
                $rand = mt_rand() / mt_getrandmax();
                if ($rand < $crashProbability) {
                    // Crash: -20 % bis -50 %
                    $newPrice = round($newPrice * (1 - mt_rand(20, 50) / 100), 2);
                } elseif ($rand < $crashProbability + $rallyProbability) {
                    // Rallye: +20 % bis +50 %
                    $newPrice = round($newPrice * (1 + mt_rand(20, 50) / 100), 2);
                }

                // ======= Saisonale leichte Schwankung (Januar-Effekt) =======
                $seasonalEffect = sin(($i / 12) * 2 * M_PI) * 0.01; // ±1 %
                $newPrice = round($newPrice * (1 + $seasonalEffect), 2);

                // ======= Begrenzung =======
                if ($newPrice <= 0) {
                    $newPrice = max(0.1, abs($lastPrice * 0.9));
                }

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

