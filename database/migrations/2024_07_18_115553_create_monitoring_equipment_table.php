<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_equipment', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('equipment_id')->unsigned()->nullable();
            $table->uuid('uuid')->unique();
            $table->string('status')->nullable();
            $table->timestamp('waktu');
            $table->timestamps();

            $table->foreign('equipment_id')->on('equipment')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_equipment');
    }
};
