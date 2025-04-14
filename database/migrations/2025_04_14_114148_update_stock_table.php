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
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('frima')->after('name')->nullable();
            $table->string('sector')->after('frima')->nullable();
            $table->string('land')->after('sector')->nullable();
            $table->text('description')->after('land')->nullable();
            $table->float('net_income')->after('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
