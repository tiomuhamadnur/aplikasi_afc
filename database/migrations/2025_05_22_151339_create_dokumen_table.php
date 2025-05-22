<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('departemen_id')->unsigned()->nullable();
            $table->bigInteger('tipe_dokumen_id')->unsigned()->nullable();
            $table->string('judul')->nullable();
            $table->string('nomor')->nullable();
            $table->string('nomor_revisi')->nullable();
            $table->date('tanggal_pengesahan')->nullable();
            $table->text('url')->nullable();
            $table->text('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('departemen_id')->on('departemen')->references('id');
            $table->foreign('tipe_dokumen_id')->on('tipe_dokumen')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
