<?php

namespace App\Http\Controllers;

use App\Models\GameTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use App\Models\Stock\Price;
use App\Models\Stock\Stock;
use App\Models\Dividend;

use App\Services\GameTimeService;
use \App\Services\DividendeService;
use App\Events\TimeskipCompleted;


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

        // Broadcast timeskip completion to refresh all connected clients (inklusive des auslösenden Users)
        broadcast(new TimeskipCompleted('Timeskip to ' . $selectedMonth . ' completed successfully'));
        \Log::info("Timeskip completed event broadcasted for month {$selectedMonth}");

        return redirect()->route('time.index');
    }

    public function skipTime($selectedMonth)
    {
        $stocks = Stock::all();
        $gameTime = new GameTime();
        $gtService = new GameTimeService();
        $divService = new DividendeService();

        // Bestimme Zielmonat (als Zahl, 0-based von UI zu 1-based konvertieren)
        $selectedMonthNum = is_numeric($selectedMonth)
            ? (int) $selectedMonth + 1  // UI ist 0-based, Monate 1-12
            : (int) date('m', strtotime($selectedMonth));

        // Hole aktuelle GameTime (z. B. "2025-04-01")
        $currentGameTime = $gameTime->getCurrentGameTime();

        // Bestimme aktuellen Monatswert (1–12)
        $currentMonth = (int) date('m', strtotime($currentGameTime->name));

        // Berechne, wie viele Monate wir vorspulen müssen
        $monthsToAdvance = ($selectedMonthNum - $currentMonth + 12) % 12;
        if ($monthsToAdvance === 0) {
            $monthsToAdvance = 12; // gleiches Monat → ganzes Jahr überspringen
        }

        // Simuliere jeden Monat bis zum Zielmonat (global)
        $newGameTimes = [];
        $currentDate = Carbon::parse($currentGameTime->name);
        for ($i = 1; $i <= $monthsToAdvance; $i++) {
            // ➕ Neuen GameTime-Eintrag für den nächsten Monat erzeugen (global)
            $currentDate = $currentDate->addMonth();
            $newGameTime = $gtService->getOrCreate($currentDate);
            $newGameTimes[] = $newGameTime;
        }

        // Für jede neue GameTime, für jede Stock Preise und Dividenden berechnen
        foreach ($newGameTimes as $newGameTime) {
            $monthIndex = (int) date('m', strtotime($newGameTime->name)) - 1; // 0-based für generatePrice

            foreach ($stocks as $stock) {
                // Letzten Preis holen
                $lastPrice = $stock->getLatestPrice();
                if ($lastPrice <= 0) {
                    $lastPrice = 100.0; // Fallback, wenn kein Preis vorhanden
                }

                // Neuen Preis berechnen mit generatePrice
                $newPriceValue = $this->generatePrice($lastPrice, $monthIndex);

                // Preis-Eintrag für diesen Monat erzeugen (manuell)
                Price::create([
                    'stock_id' => $stock->id,
                    'game_time_id' => $newGameTime->id,
                    'name' => $newPriceValue,
                ]);

                // Nächsten geplanten Dividendenzeitpunkt berechnen
                $nextDivGameTime = $stock->calculateNextDividendDate();
                if (!$nextDivGameTime) {
                    // Keine Dividende geplant → nächste Aktie
                    continue;
                }

                // Sicherstellen, dass GameTime existiert oder erzeugen
                $divGameTime = $gtService->getOrCreate($nextDivGameTime);

                // Prüfen, ob Dividende und aktueller Preis-Monat übereinstimmen
                if ($divGameTime->id === $newGameTime->id) {

                    // Verhindere doppelte Dividenden im selben Monat
                    $exists = $stock->dividends()
                        ->where('game_time_id', $divGameTime->id)
                        ->exists();

                    if (!$exists) {

                        // Dividende an Benutzer ausschütten (synchron ausführen, um sicherzustellen, dass es funktioniert)
                        $job = new \App\Jobs\ProcessDividendPayout($stock->id);
                        $job->handle();
                        \Log::info("Dividend payout job executed synchronously for stock {$stock->id}");

                        // Neue Dividende erzeugen
                        Dividend::create([
                            'stock_id' => $stock->id,
                            'game_time_id' => $divGameTime->id,
                            'amount_per_share' => fake()->randomFloat(2, 0.1, 5.0),
                        ]);

                    }
                }
            }
        }
    }



    // =========================
    // Preisgenerierung modular
    // =========================
    public function generatePrice(float $lastPrice, int $monthIndex): float
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
