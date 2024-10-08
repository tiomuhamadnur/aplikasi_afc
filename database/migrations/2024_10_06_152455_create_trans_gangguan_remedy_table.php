<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trans_gangguan_remedy', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('gangguan_id')->unsigned()->nullable();
            $table->bigInteger('remedy_id')->unsigned()->nullable();
            $table->string('remedy_other')->nullable();
            $table->dateTime('date')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('gangguan_id')->on('gangguan')->references('id');
            $table->foreign('remedy_id')->on('remedy')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trans_gangguan_remedy');
    }
};
