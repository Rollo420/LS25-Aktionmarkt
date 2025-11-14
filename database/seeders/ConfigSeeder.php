<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stock\Stock;


class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stock = Stock::first();
        $stock->configs()->create([            
            'volatility_range' => 5.0,
            'seasonal_effect_strength' => 2.0,
            'crash_interval_months' => 12,
            'rally_probability_monthly' => 0.1,
            'rally_interval_months' => 6,
        ]);
    }
}
