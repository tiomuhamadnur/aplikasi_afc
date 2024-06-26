<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_afc', function (Blueprint $table) {
            $table->id();
            $table->dateTime('time_stamp')->nullable();
            $table->string('bank')->nullable();
            $table->string('pan')->nullable();
            $table->string('elapsed_time')->nullable();
            $table->string('transaction_speed')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_afc');
    }
};
