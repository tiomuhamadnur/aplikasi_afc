<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sam_card', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('uid')->unique();
            $table->string('tid')->nullable();
            $table->string('mid')->nullable();
            $table->string('pin')->nullable();
            $table->string('mc')->nullable();
            $table->string('alokasi')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sam_card');
    }
};
