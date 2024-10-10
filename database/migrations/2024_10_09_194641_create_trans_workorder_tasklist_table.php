<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trans_workorder_tasklist', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('work_order_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('actual_duration')->nullable();
            $table->string('reference')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('work_order_id')->on('work_order')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trans_workorder_tasklist');
    }
};
