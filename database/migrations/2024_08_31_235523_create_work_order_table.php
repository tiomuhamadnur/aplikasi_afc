<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique()->nullable();
            $table->string('wo_number_sap')->unique()->nullable();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->date('date')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->bigInteger('tipe_pekerjaan_id')->unsigned()->nullable();
            $table->bigInteger('relasi_area_id')->unsigned()->nullable();
            $table->bigInteger('relasi_struktur_id')->unsigned()->nullable();
            $table->bigInteger('classification_id')->unsigned()->nullable();
            $table->bigInteger('status_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('note')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('tipe_pekerjaan_id')->on('tipe_pekerjaan')->references('id');
            $table->foreign('relasi_area_id')->on('relasi_area')->references('id');
            $table->foreign('relasi_struktur_id')->on('relasi_struktur')->references('id');
            $table->foreign('classification_id')->on('classification')->references('id');
            $table->foreign('status_id')->on('status')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order');
    }
};
