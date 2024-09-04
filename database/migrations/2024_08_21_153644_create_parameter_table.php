<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parameter', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('form_id')->unsigned()->nullable();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->string('tipe')->nullable();
            $table->string('min_value')->nullable();
            $table->string('max_value')->nullable();
            $table->bigInteger('option_form_id')->unsigned()->nullable();
            $table->bigInteger('satuan_id')->unsigned()->nullable();
            $table->string('photo_instruction')->nullable();
            $table->integer('urutan')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('form_id')->on('form')->references('id');
            $table->foreign('option_form_id')->on('option_form')->references('id');
            $table->foreign('satuan_id')->on('satuan')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parameter');
    }
};
