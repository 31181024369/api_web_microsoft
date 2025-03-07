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
        Schema::create('gift_categories', function (Blueprint $table) {
            $table->id('id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->integer('reward_point');
            $table->string('picture', 255)->nullable();
            $table->boolean('display')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_categories');
    }
};
