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
        Schema::create('quiz_member_answer', function (Blueprint $table) {
            $table->increments('id')->length(11);
            $table->integer('member_id')->nullable();
            $table->integer('quiz_id')->nullable();
            $table->integer('question_id')->nullable();
            $table->string('user_answers', 500)->nullable()->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_member_answer');
    }
};
