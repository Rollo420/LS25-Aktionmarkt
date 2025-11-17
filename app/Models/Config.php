<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Config extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'volatility_range',
        'seasonal_effect_strength',
        'crash_interval_months',
        'crash_probability_monthly',
        'rally_probability_monthly',
        'rally_interval_months',
    ];

   public function stocks()
{
    return $this->belongsToMany(Stock::class, 'config_stocks')
        ->withPivot('applied_at')
        ->withTimestamps();
}


}
