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


    public static function getCurrentGameTime(): ?GameTime
    {
        try {
            // 1️⃣ Aktuellsten Price-Eintrag holen (zuverlässigste Methode)
            $currentPrice = Price::latest('game_time_id')->first();
            if ($currentPrice && $currentPrice->gameTime) {
                \Log::debug("GameTime::getCurrentGameTime - Using price-based current time: {$currentPrice->gameTime->name}");
                return $currentPrice->gameTime;
            }

            // 2️⃣ Fallback: Neuesten GameTime basierend auf ID
            $latestGT = self::latest('id')->first();
            if ($latestGT) {
                \Log::debug("GameTime::getCurrentGameTime - Using latest GT by ID: {$latestGT->name}");
                return $latestGT;
            }

            \Log::warning("GameTime::getCurrentGameTime - No game time found");
            return null;

        } catch (\Exception $e) {
            \Log::error("GameTime::getCurrentGameTime - Error: " . $e->getMessage());
            // Fallback: Den neuesten GameTime zurückgeben
            try {
                return self::latest('id')->first();
            } catch (\Exception $e2) {
                \Log::error("GameTime::getCurrentGameTime - Fallback also failed: " . $e2->getMessage());
                return null;
            }
        }
    }



}
