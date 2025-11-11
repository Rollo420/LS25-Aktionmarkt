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
        return self::latest()->first();
    }

}
