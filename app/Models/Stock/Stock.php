<?php

namespace App\Models\Stock;

use App\Models\Dividend;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Stock\Price;
use App\Models\Stock\Transaction;

class Stock extends Model
{
    use HasFactory;

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

    public function getCurrentDividend(): float
    {
        return (float) ($this->getLatestDividend()->amount_per_share ?? 0);
    }

    public function getNextDividendDate(): ?string
    {
        $latestDividend = $this->getLatestDividend();
        return $latestDividend?->gameTime?->name;
    }

    public function getLastBuyTransactionDateForStock()
    {
        return $this->transactions()->where('type', 'buy')->first()
            ->getLastBuyTransactionDate();
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

