<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Carbon\Carbon;

use App\Models\Dividend;
use App\Models\Stock\Price;
use App\Models\Stock\Transaction;
use \App\Models\StockConfig;
use \App\Models\Config;
use App\Models\GameTime;
use App\Models\ProductType;

use Laravel\Scout\Searchable;


class Stock extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'product_type_id',
        'name',
        'firma',
        'sektor',
        'land',
        'description',
        'net_income',
        'dividend_frequency'
    ];

    /** Beziehungen **/
    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function dividends()
    {
        return $this->hasMany(Dividend::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function configs()
    {
        return $this->belongsToMany(Config::class, 'config_stocks')
            ->withPivot('applied_at')
            ->withTimestamps();
    }

    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        $array['name'] = $this->name;
        $array['firma'] = $this->firma;
        $array['sektor'] = $this->sektor;
        $array['land'] = $this->land;
        
        $array['product_type_name'] = $this->productType?->name ?? null;
        $array['price_string'] = (string) $this->getCurrentPrice();  // für Teilstring-Suche
        $array['dividend_frequency'] = (string) $this->dividend_frequency;
        $array['dividend_amount'] = (string) $this->getCurrentDividendAmount();
        $array['dividend_next_date'] = $this->calculateNextDividendDate()?->format('Y-m-d') ?? null;

        return $array;

    }

    public function getCurrentConfig()
    {
        return $this->configs()
            ->orderByDesc('pivot_applied_at')
            ->first();
    }


    /** Helper-Methoden **/
    public function getLatestPrice(): float
    {
        $latestPrice = $this->prices()->with('gameTime') // Relation laden
            ->orderByDesc('game_time_id')              // nach neuestem GameTime sortieren
            ->first();                                 // erstes Ergebnis holen

        return $latestPrice?->name ?? 0;               // falls kein Preis vorhanden, 0 zurückgeben
    }

    public function getCurrentPrice(): float
    {
        return (float) ($this->prices()->orderBy('game_time_id', 'desc')->first()->name ?? 0);
    }



    public function getLatestDividend(): ?Dividend
    {
        return $this->dividends()
            ->orderBy('game_time_id', 'ASC')  // Letzte chronologische Dividende finden
            ->get()
            ->last();  // Das letzte Element der sortierten Liste
    }

    public function getFirstDividend(): ?Dividend
    {
        return $this->dividends()->orderBy('game_time_id')->get()->first();
    }

    public function getCurrentDividendAmount(): float
    {
        return (float) ($this->getLatestDividend()->amount_per_share ?? 0);
    }

    public function getPriceAtGameTime($gameTime): float
    {
        if (!$gameTime) {
            return $this->getCurrentPrice();
        }

        $price = $this->prices()
            ->where('game_time_id', $gameTime->id)
            ->first();

        return $price ? (float) $price->name : $this->getCurrentPrice();
    }

    public function getDividendAtGameTime(GameTime $gameTime): ?Dividend
    {
        if (!$gameTime) {
            $gt = $this->getLatestDividend();
        }
        else {
            $gt = $gameTime;
        }

        return $this->dividends()
            ->where('game_time_id', '<=',$gt->id)
            ->orderBy('game_time_id', 'DESC')
            ->first();
    }

    public function calculateNextDividendDateAtGameTime($gameTime): ?Carbon
    {
        if (!$gameTime) {
            return $this->calculateNextDividendDate();
        }

        $latestDividend = $this->getDividendAtGameTime($gameTime);
        if (!$latestDividend) {
            return null;
        }

        $baseDate = Carbon::parse($latestDividend->gameTime->name);
        $monthsBetween = $this->dividend_frequency > 0 ? 12 / $this->dividend_frequency : 12;
        return $baseDate->copy()->addMonths($monthsBetween);
    }




    public function calculateNextDividendDate($date = null): ?Carbon
    {
        // 1️⃣ Basisdatum bestimmen (letzte Dividende oder übergebenes Datum)
        if (is_null($date)) {
            $latestDividend = $this->getLatestDividend();
            if (!$latestDividend) {
                \Log::debug("No latest dividend found for stock {$this->id}, cannot calculate next date");
                return null; // keine Dividende vorhanden
            }
            $baseDate = Carbon::parse($latestDividend->gameTime->name);
        } else {
            $baseDate = $date instanceof Carbon ? $date : Carbon::parse($date);
        }

        // 2️⃣ Monate zwischen Dividenden berechnen
        $monthsBetween = $this->dividend_frequency > 0 ? 12 / $this->dividend_frequency : 12;
        
        // 3️⃣ Einfache Berechnung der nächsten Dividende
        $nextDate = $baseDate->copy()->addMonths($monthsBetween);
        
        \Log::debug("Stock {$this->id}: Next dividend date calculated: {$nextDate->format('Y-m-d')} (base: {$baseDate->format('Y-m-d')}, frequency: {$this->dividend_frequency})");
        return $nextDate;
    }


    /**
     * 🚀 CRITICAL FIX: Calculate next dividend date based on current game time
     * This ensures dashboard shows correct dividend dates after time skip
     */
    public function calculateNextDividendDateAtCurrentGameTime(): ?Carbon
    {
        try {
            // Get current game time
            $currentGameTime = \App\Models\GameTime::getCurrentGameTime();
            if (!$currentGameTime) {
                \Log::warning("No current game time found for stock {$this->id}");
                return $this->calculateNextDividendDate(); // Fallback to standard calculation
            }

            \Log::debug("Stock {$this->id}: Current game time: {$currentGameTime->name}");

            // Get latest dividend (regardless of game time)
            $latestDividend = $this->getLatestDividend();
            if (!$latestDividend) {
                \Log::debug("No dividend found for stock {$this->id}");
                return $this->calculateNextDividendDate(); // Fallback
            }

            \Log::debug("Stock {$this->id}: Latest dividend at: {$latestDividend->gameTime->name}");

            // If latest dividend game time is before current game time, it's time for next dividend
            $latestGTDate = Carbon::parse($latestDividend->gameTime->name);
            $currentDate = Carbon::parse($currentGameTime->name);
            
            $monthsBetween = $this->dividend_frequency > 0 ? 12 / $this->dividend_frequency : 12;
            
            // Calculate next dividend date
            $nextDate = $latestGTDate->copy()->addMonths($monthsBetween);

            \Log::debug("Stock {$this->id}: Latest GT: {$latestGTDate->format('Y-m-d')}, Current: {$currentDate->format('Y-m-d')}, Next: {$nextDate->format('Y-m-d')}, Frequency: {$this->dividend_frequency} (months between: {$monthsBetween})");

            // If next date is in the past relative to current, it's already "due" 
            // But we still return the calculated next date for dashboard display
            return $nextDate;

        } catch (\Exception $e) {
            \Log::error("Failed to calculate next dividend at current game time for stock {$this->id}: " . $e->getMessage());
            return $this->calculateNextDividendDate(); // Fallback to standard calculation
        }
    }
    
    /**
     * Get current game time for validation
     */
    private function getCurrentGameTime()
    {
        try {
            return \App\Models\GameTime::getCurrentGameTime();
        } catch (\Exception $e) {
            \Log::warning("Could not get current game time for stock {$this->id}: " . $e->getMessage());
            return null;
        }
    }


    public function getLastBuyTransactionDateForStock()
    {
        $lastTransaction = $this->transactions()
            ->where('type', 'buy')
            ->orderBy('game_time_id', 'desc')
            ->with('gameTime')
            ->first();

        return $lastTransaction?->gameTime?->name;
    }

    public function getFirstBuyTransactionDateForStock()
    {
        $firstTransaction = $this->transactions()
            ->where('type', 'buy')
            ->orderBy('game_time_id', 'asc')
            ->with('gameTime') // eager load, um doppelte Querys zu vermeiden
            ->first();

        return $firstTransaction?->gameTime?->name;
    }

    public function getCurrentQuantity($user = null): int
    {
        $query = $this->transactions();

        if ($user) {
            $query->where('user_id', $user->id);
        }

        return (int) $query
            ->selectRaw("
            SUM(CASE WHEN type = 'buy' THEN quantity ELSE 0 END)
            - SUM(CASE WHEN type = 'sell' THEN quantity ELSE 0 END)
        AS total_quantity
        ")
            ->value('total_quantity');
    }

     public function getUserAccount()
    {
        return $this->transactions()->get()->map(function ($transaction) {
            return $transaction->user;
        })->unique();
    }



}
