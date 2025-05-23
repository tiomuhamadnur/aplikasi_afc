<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checksheet', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('work_order_id')->unsigned()->nullable();
            $table->uuid('uuid')->unique();
            $table->bigInteger('equipment_id')->unsigned()->nullable();
            $table->bigInteger('parameter_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('value')->nullable();
            $table->string('status')->nullable();
            $table->string('note')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('work_order_id')->on('work_order')->references('id');
            $table->foreign('equipment_id')->on('equipment')->references('id');
            $table->foreign('parameter_id')->on('parameter')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checksheet');
    }
};
