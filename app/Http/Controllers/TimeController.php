<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Stock\Price;
use App\Models\Stock\Stock;
use App\Services\GameTimeService;

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
        'seasonalEffectRange' => 0.015, // Etwas reduziert, um nicht zu dominant zu sein.

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
        return view('time.index', [
            'monthArray' => $this->monthArray,
            'selectedMonth' => $selectedMonth
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

        foreach ($stocks as $stock) {

            // find the last known GameTime (if any) or derive from last price
            $lastPriceRecord = $stock->prices()->orderByDesc('game_time_id')->orderByDesc('created_at')->first();
            if ($lastPriceRecord && isset($lastPriceRecord->game_time_id) && $lastPriceRecord->game_time_id) {
                $lastGameTime = \App\Models\GameTime::find($lastPriceRecord->game_time_id);
                $lastMonth = $lastGameTime?->month_id ?? (int) date('m');
                $lastYear = $lastGameTime?->current_year ?? (int) date('Y');
                $lastPrice = $lastPriceRecord->name;
            } else {
                // fallback to created_at of last price
                $lastPrice = $lastPriceRecord ? $lastPriceRecord->name : 100;
                $lastDateObj = $lastPriceRecord ? Carbon::parse($lastPriceRecord->created_at) : Carbon::now();
                $lastMonth = (int) $lastDateObj->format('m');
                $lastYear = (int) $lastDateObj->format('Y');
            }

            // target month number from selectedMonth string like 'April' or numeric input
            $selectedMonthNum = is_numeric($selectedMonth) ? (int) $selectedMonth : (int) date('m', strtotime($selectedMonth));

            // compute how many months to advance from lastMonth/lastYear to reach selectedMonth in the future
            $monthsToAdvance = 0;
            $currentMonth = $lastMonth;
            $currentYear = $lastYear;
            // move at least one month forward (skip to next occurrence of selectedMonth)
            while (true) {
                // advance one month
                $currentMonth++;
                if ($currentMonth > 12) { $currentMonth = 1; $currentYear++; }
                $monthsToAdvance++;
                if ($currentMonth === $selectedMonthNum) break;
                // safety: avoid infinite loops
                if ($monthsToAdvance > 1200) break;
            }

            for ($i = 0; $i < $monthsToAdvance; $i++) {
                // compute month/year for this new period
                $lastMonth++;
                if ($lastMonth > 12) { $lastMonth = 1; $lastYear++; }

                // find or create the GameTime for this month/year using service
                $gameTime = $gtService->getOrCreate($lastYear, $lastMonth);

                // generate price for this month
                $newPrice = $this->generatePrice($lastPrice, $i);

                // create Price linked to this GameTime; do NOT modify created_at — keep real-life timestamp
                $price = new Price();
                $price->stock_id = $stock->id;
                $price->name = $newPrice;
                $price->game_time_id = $gameTime->id;
                $price->save();

                $lastPrice = $newPrice;
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
