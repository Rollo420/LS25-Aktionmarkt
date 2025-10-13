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
    /**
     * Die Attribute, die massenweise zuweisbar sind.
     *
     * @var array
     */
    protected $fillable = ['name',];

    /**
     * Beziehung: Eine Aktie hat viele Preise.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    /**
     * Beziehung: Eine Aktie hat viele Transaktionen.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function dividends()
    {
        return $this->hasMany(Dividend::class);
    }

    public function getCurrentPrice(){
       return $this->prices()->latest('created_at')->first()->name ?? 0;
    }

    public function getDividendenDate(){
       return $this->dividends()->latest('created_at')->first()->distribution_date ?? null;
    }
}
