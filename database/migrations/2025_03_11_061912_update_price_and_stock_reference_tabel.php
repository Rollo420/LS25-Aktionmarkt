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
        Schema::table('stocks', function (Blueprint $table): void {
            if (Schema::hasColumn('stocks', 'price_id')) {
                $table->dropForeign(['price_id']);
                $table->dropColumn('price_id');
            }
        });

        Schema::table('prices', function (Blueprint $table) : void {
            $table->foreign('stock_id')->references('id')->on('stocks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table): void {
            $table->unsignedBigInteger('price_id');
            $table->foreign('price_id')->references('id')->on('prices');
        });

        Schema::table('prices', function (Blueprint $table): void {
            $table->dropForeign(['stock_id']);
        });
    }
};
