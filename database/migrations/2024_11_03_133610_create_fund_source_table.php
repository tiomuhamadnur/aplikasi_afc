<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fund_source', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('fund_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->integer('balance')->nullable();
            $table->integer('current_balance')->nullable();
            $table->date('start_period')->nullable();
            $table->date('end_period')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('fund_id')->on('fund')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_source');
    }
};
