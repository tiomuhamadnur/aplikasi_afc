<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->uuid('uuid')->unique();
            $table->bigInteger('tipe_equipment_id')->unsigned()->nullable();
            $table->string('description')->nullable();
            $table->string('status')->default('active')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('tipe_equipment_id')->on('tipe_equipment')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form');
    }
};
