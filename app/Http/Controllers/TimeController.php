<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\GameTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use App\Models\Stock\Price;
use App\Models\Stock\Stock;
use App\Models\Dividend;

use App\Services\GameTimeService;
use \App\Services\DividendeService;
use \App\Services\StockService;


use App\Events\TimeskipCompleted;
use Illuminate\Support\Facades\Cache;
use App\Models\User;


class TimeController extends Controller
{
    public $monthArray = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

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

        // 🚀 CRITICAL FIX: Clear all relevant caches after time skip
        $this->clearDividendCaches();

        // Broadcast timeskip completion to refresh all connected clients (inklusive des auslösenden Users)
        broadcast(new TimeskipCompleted('Timeskip to ' . $selectedMonth . ' completed successfully'));
        \Log::info("Timeskip completed event broadcasted for month {$selectedMonth}");

        return redirect()->route('time.index');
    }


    /**
     * Clear all dividend-related caches after time skip
     * Ensures dashboard shows correct data immediately
     */
    private function clearDividendCaches()
    {
        try {
            // Clear general dividend caches
            Cache::tags(['dividends', 'dashboard', 'stocks'])->flush();
            
            // Clear user-specific dividend caches
            $users = User::all();
            foreach ($users as $user) {
                Cache::tags(['user_dividends_' . $user->id])->flush();
                Cache::tags(['user_dashboard_' . $user->id])->flush();
                Cache::tags(['user_stocks_' . $user->id])->flush();
            }
            
            // Clear stock-specific caches
            Cache::tags(['stocks', 'prices', 'dividends'])->flush();
            
            // 🚀 CRITICAL FIX: Clear ALL Laravel caches
            \Cache::flush(); // Clear entire cache
            
            // Force route cache clearing if exists
            try {
                \Artisan::call('route:clear');
                \Log::info("Route cache cleared");
            } catch (\Exception $e) {
                \Log::warning("Route cache clear failed: " . $e->getMessage());
            }
            
            // Force view cache clearing if exists
            try {
                \Artisan::call('view:clear');
                \Log::info("View cache cleared");
            } catch (\Exception $e) {
                \Log::warning("View cache clear failed: " . $e->getMessage());
            }
            
            \Log::info("All dividend-related caches cleared after time skip");
            
        } catch (\Exception $e) {
            \Log::error("Failed to clear caches after time skip: " . $e->getMessage());
        }
    }

    public function skipTime($selectedMonth)
    {
        $stocks = Stock::with('configs')->get();
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

        StockService::processNewTimeSteps($newGameTimes, $stocks);

        
    }



    

    
}
