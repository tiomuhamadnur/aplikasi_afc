<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fund', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->uuid('uuid')->unique();
            $table->string('code')->nullable();
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->bigInteger('divisi_id')->unsigned()->nullable();
            $table->bigInteger('status_budgeting_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('divisi_id')->on('fund')->references('id');
            $table->foreign('status_budgeting_id')->on('status_budgeting')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund');
    }
};
