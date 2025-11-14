<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Stock\Stock;

class Config extends Model
{
    protected $fillable = [
        'volatility_range',
        'seasonal_effect_strength',
        'crash_interval_months',
        'crash_probability_monthly',
        'rally_probability_monthly',
        'rally_interval_months',
    ];

    public function stock_configs()
    {
        return $this->hasManyThrough(StockConfig::class, Stock::class, 'id', 'config_id', 'id', 'stock_id');
    }
}
