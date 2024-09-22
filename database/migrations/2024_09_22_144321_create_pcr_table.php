<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pcr', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('tipe_equipment_id')->unsigned()->nullable();
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->bigInteger('problem_id')->unsigned()->nullable();
            $table->bigInteger('cause_id')->unsigned()->nullable();
            $table->bigInteger('remedy_id')->unsigned()->nullable();
            $table->bigInteger('classification_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('tipe_equipment_id')->on('tipe_equipment')->references('id');
            $table->foreign('category_id')->on('category')->references('id');
            $table->foreign('problem_id')->on('problem')->references('id');
            $table->foreign('cause_id')->on('cause')->references('id');
            $table->foreign('remedy_id')->on('remedy')->references('id');
            $table->foreign('classification_id')->on('classification')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pcr');
    }
};
