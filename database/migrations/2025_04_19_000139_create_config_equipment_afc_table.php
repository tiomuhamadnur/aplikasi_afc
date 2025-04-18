<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('config_equipment_afc', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('line_code')->nullable();
            $table->string('line_id')->nullable();
            $table->string('station_name')->nullable();
            $table->string('station_code')->nullable();
            $table->string('station_id')->nullable();
            $table->string('equipment_type_code')->nullable();
            $table->string('equipment_type_id')->nullable();
            $table->string('equipment_name')->nullable();
            $table->string('equipment_id')->nullable();
            $table->string('corner_id')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('direction')->nullable();
            $table->string('x_coordinate')->nullable();
            $table->string('y_coordinate')->nullable();
            $table->string('ns_device_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config_equipment_afc');
    }
};
