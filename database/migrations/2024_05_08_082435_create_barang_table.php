<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('merk')->nullable();
            $table->bigInteger('tipe_barang_id')->unsigned()->nullable();
            $table->bigInteger('relasi_area_id')->unsigned()->nullable();
            $table->bigInteger('satuan_id')->unsigned()->nullable();
            $table->string('spesifikasi')->nullable();
            $table->string('material_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('deskripsi')->nullable();
            $table->string('photo')->nullable();
            $table->integer('harga')->nullable();
            $table->date('expired_date')->nullable();
            $table->uuid('uuid')->unique()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('tipe_barang_id')->on('tipe_barang')->references('id');
            $table->foreign('relasi_area_id')->on('relasi_area')->references('id');
            $table->foreign('satuan_id')->on('satuan')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
