<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('relasi_struktur_id')->unsigned()->nullable();
            $table->bigInteger('jabatan_id')->unsigned()->nullable();
            $table->bigInteger('tipe_employee_id')->unsigned()->nullable();
            $table->integer('priority')->nullable();
            $table->string('name')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('relasi_struktur_id')->on('relasi_struktur')->references('id');
            $table->foreign('jabatan_id')->on('jabatan')->references('id');
            $table->foreign('tipe_employee_id')->on('tipe_employee')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval');
    }
};
