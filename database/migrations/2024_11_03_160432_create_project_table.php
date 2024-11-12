<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('fund_source_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('value')->nullable();
            $table->integer('current_value')->nullable();
            $table->date('start_period')->nullable();
            $table->date('end_period')->nullable();
            $table->string('status')->nullable();
            $table->string('attachment')->nullable();
            $table->bigInteger('relasi_struktur_id')->unsigned()->nullable();
            $table->bigInteger('departemen_id')->unsigned()->nullable();
            $table->bigInteger('perusahaan_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('fund_source_id')->on('fund_source')->references('id');
            $table->foreign('relasi_struktur_id')->on('relasi_struktur')->references('id');
            $table->foreign('departemen_id')->on('departemen')->references('id');
            $table->foreign('perusahaan_id')->on('perusahaan')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};
