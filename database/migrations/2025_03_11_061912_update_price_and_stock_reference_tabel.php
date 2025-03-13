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
                $table->dropColumn(columns: 'price_id');
            }
        });

        Schema::table('prices', function (Blueprint $table): void {
            $table->foreignId('stock_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prices', function (Blueprint $table): void {
            $table->dropForeign(['stock_id']);
            $table->dropColumn('stock_id');
        });

        Schema::table('stocks', function (Blueprint $table): void {
            $table->unsignedBigInteger('price_id');
            $table->foreign('price_id')->references('id')->on('prices');
        });
    }
};
