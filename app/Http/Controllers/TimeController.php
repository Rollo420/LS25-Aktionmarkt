<?php

namespace App\Http\Controllers;

use App\Models\GameTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Stock\Price;
use App\Models\Stock\Stock;

use App\Services\GameTimeService;
use \App\Services\DividendeService;


class TimeController extends Controller
{
    public $monthArray = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    // =========================
    // Konfiguration (flexibel)
    // =========================
    protected $config = [
        // ==============================
        // Excel-artige Zufallsbewegung (Tägliche/Monatliche Volatilität)
        // ==============================
        'useExcelRandom' => true,      // Tägliche kleine Schwankung beibehalten (wichtig für Leben)
        'excelRandomRange' => 0.04,    // Erhöht auf ±4% pro Monat/Periode. Simuliert die normale marktbreite Volatilität.

        // ==============================
        // Saisonaler Effekt
        // ==============================
        'useSeasonalEffect' => true,   // Saisonalität beibehalten (z.B. "Sell in May")
        'seasonalEffectRange' => 0.026, // Etwas reduziert, um nicht zu dominant zu sein.

        // ==============================
        // Crash / Rallye Simulation
        // ==============================
        'useCrashRally' => true,       // Crashs und Rallyes beibehalten

        // Crash-Wahrscheinlichkeit: Alle 20 Jahre
        'crashProbability' => 1 / 240, // 1 Ereignis in 240 Monaten (20 Jahre)

        // Rallye-Wahrscheinlichkeit: Alle 30 Jahre. Rallyes sind seltener und oft weniger extrem als Crashs.
        'rallyProbability' => 1 / 360, // 1 Ereignis in 360 Monaten (30 Jahre) 
    ];



    public function index()
    {
        $selectedMonth = session('selectedMonth', 'None');

        // Aktuelle GameTime holen
        $currentGameTimeFormatted = GameTime::getCurrentGameTime()->name;

        return view('time.index', [
            'monthArray' => $this->monthArray,
            'selectedMonth' => $selectedMonth,
            'currentGameTime' => $currentGameTimeFormatted
        ]);
    }

    public function update(Request $request)
    {
        $selectedMonth = $request->input('choose');
        $this->skipTime($selectedMonth);
        session(['selectedMonth' => $selectedMonth]);
        return redirect()->route('time.index');
    }

    public function skipTime($selectedMonth)
    {
        $stocks = Stock::all();
        $gtService = new GameTimeService();

        $selectedMonthNum = is_numeric($selectedMonth)
            ? (int) $selectedMonth
            : (int) date('m', strtotime($selectedMonth));

        foreach ($stocks as $stock) {

            // nur die letzte GameTime
            $currentGameTime = GameTime::getCurrentGameTime();
            $lastDateString = $currentGameTime->name;

            // Hole den letzten Preis für diese Aktie
            $lastPriceRecord = $stock->prices()->orderByDesc('game_time_id')->first();
            $lastPrice = $lastPriceRecord ? $lastPriceRecord->name : 100;

            // Berechne Monate bis zum nächsten gewünschten selectedMonth
            $lastMonth = (int) date('m', strtotime($lastDateString));
            $monthsToAdvance = ($selectedMonthNum - $lastMonth + 12) % 12;
            if ($monthsToAdvance === 0) {
                $monthsToAdvance = 12; // wenn gleiche Monatsnummer, spring gleich ein ganzes Jahr
            }

            for ($i = 1; $i <= $monthsToAdvance; $i++) {
                // GameTime erzeugen
                $nextDateString = Carbon::parse($gtService->advanceMonthsStrtotime($lastDateString, 1));
                $gameTime = $gtService->getOrCreate($nextDateString);

                #if($gameTime->name == $stock->getNextDividendDate()){
                    //Dividende auszahlen
                    $divService = new DividendeService();
                    $divService->shareDividendeToUsers($stock);

                #}

                // Preis erzeugen - nur wenn noch kein Preis für diese GameTime existiert
                $existingPrice = Price::where('stock_id', $stock->id)
                    ->where('game_time_id', $gameTime->id)
                    ->first();

                if (!$existingPrice) {
                    $newPrice = $this->generatePrice($lastPrice, $i);

                    $price = new Price();
                    $price->stock_id = $stock->id;
                    $price->name = $newPrice;
                    $price->game_time_id = $gameTime->id;
                    $price->save();

                    // Update lastPrice für nächste Iteration
                    $lastPrice = $newPrice;
                } else {
                    // Verwende den bestehenden Preis als Basis für die nächste Berechnung
                    $lastPrice = $existingPrice->name;
                }

                $lastDateString = $nextDateString;
            }
        }
    }


    // =========================
    // Preisgenerierung modular
    // =========================
    protected function generatePrice(float $lastPrice, int $monthIndex): float
    {
        $price = $lastPrice;

        if ($this->config['useExcelRandom']) {
            $price = $this->applyExcelRandom($price, $this->config['excelRandomRange']);
        }

        if ($this->config['useCrashRally']) {
            $price = $this->applyCrashRally($price);
        }

        if ($this->config['useSeasonalEffect']) {
            $price = $this->applySeasonalEffect($price, $monthIndex, $this->config['seasonalEffectRange']);
        }

        // Mindestpreis
        if ($price <= 0)
            $price = max(0.1, abs($lastPrice * 0.9));

        return round($price, 2);
    }

    // =========================
    // Excel-artige Zufallsbewegung
    // =========================
    protected function applyExcelRandom(float $price, float $range): float
    {
        $randomFactor = (mt_rand() / mt_getrandmax() - 0.5) * 2 * $range; // [-range, +range]
        return $price * (1 + $randomFactor);
    }

    // =========================
    // Crash/Rallye Simulation
    // =========================
    protected function applyCrashRally(float $price): float
    {
        $rand = mt_rand() / mt_getrandmax();
        if ($rand < $this->config['crashProbability']) {
            $price *= 1 - mt_rand(20, 50) / 100; // Crash -20% bis -50%
        } elseif ($rand < $this->config['crashProbability'] + $this->config['rallyProbability']) {
            $price *= 1 + mt_rand(20, 50) / 100; // Rally +20% bis +50%
        }
        return $price;
    }

    // =========================
    // Saisonaler Effekt
    // =========================
    protected function applySeasonalEffect(float $price, int $monthIndex, float $range): float
    {
        $effect = sin(($monthIndex / 12) * 2 * M_PI) * $range; // ±range
        return $price * (1 + $effect);
    }
}
