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
        $defaultDate = '00-Jun-01 00:00:00';
        Schema::table('prices', function (Blueprint $table) use ($defaultDate) {
            $table->dropColumn(['month', 'year']);
            $table->datetime('date')->default($defaultDate)->after('stock_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('prices', function (Blueprint $table) {
            $table->string('month')->after('id');
            $table->integer('year')->after('month');
            $table->dropColumn('date');
        });
    }
};
