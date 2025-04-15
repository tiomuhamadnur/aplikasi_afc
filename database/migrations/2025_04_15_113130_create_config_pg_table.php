<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('config_pg', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('station_name')->nullable();
            $table->string('station_code')->nullable();
            $table->string('station_id')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->integer('order')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config_pg');
    }
};
