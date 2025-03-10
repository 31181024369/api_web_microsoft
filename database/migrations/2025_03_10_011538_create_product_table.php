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
        Schema::create('product', function (Blueprint $table) {
            $table->bigIncrements('product_id')->length(20);
            $table->string('title',250)->nullable();
            $table->string('picture',250)->nullable()->default('NULL');
            $table->longText('description')->nullable();
            $table->tinyInteger('display')->length(4)->nullable()->default('1');
            $table->string('friendly_url',250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
