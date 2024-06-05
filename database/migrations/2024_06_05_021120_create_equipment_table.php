<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->unique()->nullable();
            $table->uuid('uuid')->unique();
            $table->string('equipment_number')->unique()->nullable();
            $table->bigInteger('tipe_equipment_id')->unsigned()->nullable();
            $table->bigInteger('relasi_area_id')->unsigned()->nullable();
            $table->bigInteger('relasi_struktur_id')->unsigned()->nullable();
            $table->bigInteger('arah_id')->unsigned()->nullable();
            $table->string('photo')->nullable();
            $table->string('status')->nullable()->default('active');
            $table->string('deskripsi')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('tipe_equipment_id')->on('tipe_equipment')->references('id');
            $table->foreign('relasi_area_id')->on('relasi_area')->references('id');
            $table->foreign('relasi_struktur_id')->on('relasi_struktur')->references('id');
            $table->foreign('arah_id')->on('arah')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
