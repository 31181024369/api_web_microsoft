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
        Schema::create('quiz', function (Blueprint $table) {
            $table->increments('id')->length(11);
            $table->string('name', 500)->nullable()->default('');
            $table->text('description')->nullable();
            $table->string('mem_code', 150)->nullable();
            $table->string('picture',250)->nullable()->default('NULL');
            $table->string('diffculty', 200)->nullable()->default('NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz');
    }
};
