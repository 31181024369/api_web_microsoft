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
        Schema::create('theory_category', function (Blueprint $table) {
            $table->increments('cat_id')->length(11);
            $table->string('title', 255)->default('');
            $table->string('description', 255)->default('');
            $table->string('friendly_url', 255)->default('');
            $table->integer('parentid')->length(11)->default('0');
            $table->tinyInteger('display')->length(4)->default('1');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theory_category');
    }
};
