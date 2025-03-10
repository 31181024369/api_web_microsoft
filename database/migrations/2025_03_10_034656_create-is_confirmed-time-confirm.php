<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('gift_history', function (Blueprint $table) {
            $table->boolean('is_confirmed')->default(false);
            $table->timestamp('confirmed_at')->nullable();
        });
    }


    public function down(): void
    {
        Schema::table('gift_history', function (Blueprint $table) {
            $table->dropColumn(['is_confirmed', 'confirmed_at']);
        });
    }
};
