<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Dividend;
use App\Models\Stock\Price;
use App\Models\Stock\Transaction;

class GameTime extends Model
{
    /** @use HasFactory<\Database\Factories\GameTimeFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    public function dividends(){
        return $this->hasMany(Dividend::class);
    }

    public function prices(){
        return $this->hasMany(Price::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public static function getCurrentGameTime()
    {
        // Aktuellsten Price-Eintrag holen
        $currentPrice = Price::latest('game_time_id')->first();

        if ($currentPrice) {
            return $currentPrice->gameTime;
        }

        // Fallback: Falls kein Price existiert, den neuesten GameTime zurückgeben
        return self::latest('id')->first();
    }



}
