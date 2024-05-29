<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relasi_area', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lokasi_id')->unsigned()->nullable();
            $table->bigInteger('sub_lokasi_id')->unsigned()->nullable();
            $table->bigInteger('detail_lokasi_id')->unsigned()->nullable();
            $table->uuid('uuid')->unique()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('lokasi_id')->on('lokasi')->references('id');
            $table->foreign('sub_lokasi_id')->on('sub_lokasi')->references('id');
            $table->foreign('detail_lokasi_id')->on('detail_lokasi')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relasi_area');
    }
};
