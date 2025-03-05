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
        Schema::create('theory', function (Blueprint $table) {
            $table->id('theory_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('friendly_url', 255);
            $table->string('meta_keywords', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('picture', 255)->nullable();
            $table->boolean('display');
            $table->unsignedInteger('cat_id')->nullable();
            $table->timestamps();

            $table->foreign('cat_id')->references('cat_id')->on('theory_category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theory');
    }
};
