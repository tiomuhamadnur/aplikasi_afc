<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->uuid('uuid')->unique();
            $table->string('code')->nullable();
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->bigInteger('tipe_equipment_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('category_id')->on('category')->references('id');
            $table->foreign('tipe_equipment_id')->on('tipe_equipment')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem');
    }
};
