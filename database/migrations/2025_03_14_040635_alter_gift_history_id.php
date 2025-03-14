<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('gift_history', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('gift_history', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
        });
    }

    public function down()
    {
        Schema::table('gift_history', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};
