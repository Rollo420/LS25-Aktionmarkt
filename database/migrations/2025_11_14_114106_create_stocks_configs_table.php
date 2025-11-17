<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
        
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            
            $table->float('volatility_range')->default(0.04);
            $table->float('seasonal_effect_strength')->default(0.026);
            $table->float('crash_probability_monthly')->default(1);
            $table->integer('crash_interval_months')->default(240);
            $table->float('rally_probability_monthly')->default(1);
            $table->integer('rally_interval_months')->default(360);
            $table->timestamps();
        });
        
        Schema::create('config_stocks', function (Blueprint $table) {
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->foreignId('config_id')->constrained('configs')->onDelete('cascade');
            $table->datetime('applied_at', 3)->default(DB::raw('CURRENT_TIMESTAMP(3)'));

            // Eindeutigkeit sicherstellen: kein Duplikat-Paar
            $table->primary(['stock_id', 'applied_at']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
        Schema::dropIfExists('stocks_configs');
    }
};

//    public function users(): hasManyThrough
//    {
//        return $this->hasManyThrough(User::class, UserBoard::class, 'board_id', 'id', 'id', 'user_id');
//    }

//$fillable = ['stock_id', config_id]