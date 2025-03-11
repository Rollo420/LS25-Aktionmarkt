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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id()->autoIncrement()->unique();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('stock_id');
            $table->boolean('status');
            $table->integer('quantity');
            $table->timestamps();
            
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('stock_id')->references('id')->on('stocks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->dropForeign(['account_id']);
            $table->dropForeign(['stock_id']);
        });

        Schema::dropIfExists('transactions');
    }
};
