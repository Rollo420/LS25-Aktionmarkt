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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id()->autoIncrement()->unique()->nullable(false);
            $table->unsignedBigInteger('username_id');
            $table->unsignedBigInteger('password_id');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            //$table->foreign('password_id')->references('id')->on('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
