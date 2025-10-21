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
        if (Schema::hasTable('prices') && !Schema::hasColumn('prices', 'date')) {
            Schema::table('prices', function (Blueprint $table) {
                $table->date('date')->nullable()->after('game_time_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('prices') && Schema::hasColumn('prices', 'date')) {
            Schema::table('prices', function (Blueprint $table) {
                $table->dropColumn('date');
            });
        }
    }
};
