<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Stock\Stock;

class StockConfig extends Model
{
    protected $fillable = [
        'stock_id',
        'config_id',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function config()
    {
        return $this->belongsTo(Config::class);
    }
}
