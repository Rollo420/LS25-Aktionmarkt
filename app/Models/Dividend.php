<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stock\Stock;

class Dividend extends Model
{
    /** @use HasFactory<\Database\Factories\DividendFactory> */
    use HasFactory;

    public function stock() {
        return $this->belongsTo(Stock::class);
    }

    public function gameTime(){
        return $this->belongsTo(GameTime::class);
    }
}
