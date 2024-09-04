<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('option_form', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->uuid('uuid')->unique();
            $table->string('code')->nullable();
            $table->text('value')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('option_form');
    }
};
