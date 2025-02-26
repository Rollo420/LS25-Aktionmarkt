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
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['username_id']);
            $table->renameColumn('username_id', 'username');
        });
        
        Schema::dropIfExists('usernames');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('usernames', function (Blueprint $table) {
            $table->id()->autoIncrement()->unique()->nullable(false);
            $table->string('name');
            $table->timestamps();
            $table->renameColumn('username', 'username_id');
            $table->foreign('username_id')->references('id')->on('usernames');
        });
    }
};
