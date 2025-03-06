<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTheoryIdToQuizTable extends Migration
{
    public function up()
    {
        Schema::table('quiz', function (Blueprint $table) {
            $table->unsignedBigInteger('theory_id')->nullable()->after('id');
            $table->foreign('theory_id')->references('theory_id')->on('theory')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('quiz', function (Blueprint $table) {
            $table->dropForeign(['theory_id']);
            $table->dropColumn('theory_id');
        });
    }
}
