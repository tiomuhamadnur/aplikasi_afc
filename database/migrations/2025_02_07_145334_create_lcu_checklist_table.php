<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lcu_checklist', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->dateTime('date')->nullable();
            $table->boolean('mks_status')->nullable();
            $table->boolean('lighting_status')->nullable();
            $table->boolean('cctv_status')->nullable();
            $table->boolean('ac_status')->nullable();
            $table->boolean('room_cleanliness')->nullable();
            $table->boolean('server_status')->nullable();
            $table->boolean('server_alert')->nullable();
            $table->boolean('switch_status')->nullable();
            $table->boolean('switch_alert')->nullable();
            $table->boolean('ups_status')->nullable();
            $table->boolean('ups_alert')->nullable();
            $table->boolean('cable_status')->nullable();
            $table->decimal('room_temperature', 5, 2)->nullable();
            $table->string('room_temp_photo')->nullable();
            $table->decimal('rack_temperature', 5, 2)->nullable();
            $table->string('rack_temp_photo')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('functional_location_id')->unsigned()->nullable();
            $table->text('remark')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('functional_location_id')->on('functional_location')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lcu_checklist');
    }
};
