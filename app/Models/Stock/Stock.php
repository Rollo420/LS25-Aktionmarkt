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

    protected $fillable = ['name'];

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
        return (float) ($this->prices()->latest('created_at')->first()->name ?? 0);
    }

    public function getCurrentPrice(): float
    {
        return (float) ($this->prices()->orderBy('game_time_id', 'desc')->first()->name ?? 0);
    }

    public function getLatestDividend(): ?Dividend
    {
        return $this->dividends()->latest('created_at')->first();
    }

    public function getCurrentDividend(): float
    {
        return (float) ($this->getLatestDividend()->amount_per_share ?? 0);
    }

    public function getNextDividendDate(): ?string
    {
        return optional($this->getLatestDividend())->distribution_date;
    }
}
