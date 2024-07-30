<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_barang', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('equipment_id')->unsigned()->nullable();
            $table->bigInteger('barang_id')->unsigned()->nullable();
            $table->uuid('uuid')->unique();
            $table->integer('qty')->nullable();
            $table->date('tanggal')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('equipment_id')->on('equipment')->references('id');
            $table->foreign('barang_id')->on('barang')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_barang');
    }
};
