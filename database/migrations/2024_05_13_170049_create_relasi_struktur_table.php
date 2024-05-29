<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relasi_struktur', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('direktorat_id')->unsigned()->nullable();
            $table->bigInteger('divisi_id')->unsigned()->nullable();
            $table->bigInteger('departemen_id')->unsigned()->nullable();
            $table->bigInteger('seksi_id')->unsigned()->nullable();
            $table->uuid('uuid')->unique()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('direktorat_id')->on('direktorat')->references('id');
            $table->foreign('divisi_id')->on('divisi')->references('id');
            $table->foreign('departemen_id')->on('departemen')->references('id');
            $table->foreign('seksi_id')->on('seksi')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relasi_struktur');
    }
};
