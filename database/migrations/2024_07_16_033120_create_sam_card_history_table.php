<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sam_card_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('sam_card_id')->unsigned()->nullable();
            $table->bigInteger('relasi_area_id')->unsigned()->nullable();
            $table->string('pg_id')->nullable();
            $table->string('type')->nullable();
            $table->date('tanggal')->nullable();
            $table->timestamps();

            $table->foreign('sam_card_id')->on('sam_card')->references('id');
            $table->foreign('relasi_area_id')->on('relasi_area')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sam_card_history');
    }
};
