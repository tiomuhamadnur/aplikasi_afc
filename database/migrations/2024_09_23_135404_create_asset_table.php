<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('parent_id')->on('asset')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset');
    }
};
