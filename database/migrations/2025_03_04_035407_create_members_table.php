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
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id')->length(11);
            $table->string('username', 150)->nullable()->default('');
            $table->string('mem_code', 150)->nullable();
            $table->string('email', 200)->nullable()->default('NULL');
            $table->string('password', 100)->default('');
            $table->integer('gender')->defaut(0);
            $table->string('address', 200)->nullable()->default('NULL');
            $table->string('company', 250)->nullable()->default('NULL');
            $table->string('full_name', 250)->nullable();
            $table->string('avatar', 255)->nullable()->default('NULL');
            $table->string('phone', 50)->nullable()->default('NULL');
            $table->string('provider',255)->nullable();
            $table->string('district', 200)->nullable()->default('NULL');
            $table->string('ward', 200)->nullable()->default('NULL');
            $table->string('city_province', 200)->nullable()->default('NULL');
            $table->integer('status')->defaut(0);
            $table->integer('m_status')->defaut(0);
            $table->string('date_join',200)->nullable();
            $table->string('password_token',255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
