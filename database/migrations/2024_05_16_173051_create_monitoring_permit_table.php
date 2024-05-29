<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_permit', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('nomor')->nullable()->unique();
            $table->string('deskripsi')->nullable();
            $table->date('tanggal_expired')->nullable();
            $table->string('status')->nullable();
            $table->uuid('uuid')->unique();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('departemen_id')->unsigned()->nullable();
            $table->bigInteger('tipe_permit_id')->unsigned()->nullable();
            $table->bigInteger('relasi_area_id')->unsigned()->nullable();
            $table->bigInteger('tipe_pekerjaan_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->on('users')->references('id');
            $table->foreign('departemen_id')->on('departemen')->references('id');
            $table->foreign('tipe_permit_id')->on('tipe_permit')->references('id');
            $table->foreign('relasi_area_id')->on('relasi_area')->references('id');
            $table->foreign('tipe_pekerjaan_id')->on('tipe_pekerjaan')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_permit');
    }
};
