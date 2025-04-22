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
        // Only rename and update if the column exists
        if (Schema::hasColumn('transactions', 'account_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                // Rename the column 'account_id' to 'user_id'
                $table->renameColumn('account_id', 'user_id');
            });
        }
        // Drop old foreign key if it exists, then add the correct one
        Schema::table('transactions', function (Blueprint $table) {
            // Drop old foreign key if it exists
            try {
                $table->dropForeign(['account_id']);
            } catch (\Exception $e) {}
            try {
                $table->dropForeign(['user_id']);
            } catch (\Exception $e) {}
            // Add new foreign key
            if (Schema::hasColumn('transactions', 'user_id')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('transactions', 'user_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->renameColumn('user_id', 'account_id');
            });
        }
        Schema::table('transactions', function (Blueprint $table) {
            try {
                $table->dropForeign(['user_id']);
            } catch (\Exception $e) {}
            try {
                $table->dropForeign(['account_id']);
            } catch (\Exception $e) {}
            if (Schema::hasColumn('transactions', 'account_id')) {
                $table->foreign('account_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }
};
