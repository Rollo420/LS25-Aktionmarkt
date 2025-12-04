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
	    // All model attributes are made searchable
        $array = $this->toArray();

		// Then we add some additional fields
        $array['name'] = $this->name;
        $array['product_type_name'] = $this->productType->name;
        $array['firma'] = $this->firma;
        $array['sektor'] = $this->sektor;
        $array['land'] = $this->land;
        $array['price'] = $this->getCurrentPrice();
        $array['dividend_amount'] = $this->getCurrentDividendAmount();

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
        return $this->dividends()->orderBy('game_time_id', 'DESC')->get()->first();
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
        // 1️⃣ Basisdatum bestimmen
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
        \Log::debug("Stock {$this->id} dividend_frequency: {$this->dividend_frequency}, monthsBetween: {$monthsBetween}");

        // 3️⃣ Nächste Dividende berechnen
        $nextDate = $baseDate->copy()->addMonths($monthsBetween);
        \Log::debug("Next dividend date for stock {$this->id}: {$nextDate->format('Y-m-d')} (base: {$baseDate->format('Y-m-d')})");
        return $nextDate;
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
