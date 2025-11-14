<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("stocks_configs", function (Blueprint $table) {
            
        });
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->references('id')->on('stocks')->onDelete('cascade');
            $table->float('volatility_range');
            $table->float('seasonal_effect_strength');
            $table->integer('crash_interval_months');
            $table->float('rally_probability_monthly');
            $table->float('rally_interval_months');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks_configs');
    }
};

//    public function users(): hasManyThrough
//    {
//        return $this->hasManyThrough(User::class, UserBoard::class, 'board_id', 'id', 'id', 'user_id');
//    }

//$fillable = ['stock_id', config_id]