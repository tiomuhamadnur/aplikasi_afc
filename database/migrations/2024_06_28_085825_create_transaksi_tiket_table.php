<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_tiket', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type')->nullable();
            $table->uuid('uuid')->unique();
            $table->string('transaction_id')->unique()->nullable();
            $table->string('device')->nullable();
            $table->string('corner_id')->nullable();
            $table->string('pg_id')->nullable();
            $table->string('pan')->nullable();
            $table->float('transaction_amount')->nullable();
            $table->float('balance_before')->nullable();
            $table->float('balance_after')->nullable();
            $table->string('card_type')->nullable();
            $table->dateTime('tap_in_time')->nullable();
            $table->string('tap_in_station')->nullable();
            $table->dateTime('tap_out_time')->nullable();
            $table->string('tap_out_station')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_tiket');
    }
};
