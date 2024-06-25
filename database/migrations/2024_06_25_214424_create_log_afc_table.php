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
            $table->string('pan');
            $table->string('elapsed_time');
            $table->string('transaction_speed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_afc');
    }
};
