<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trans_workorder_user', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('work_order_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('work_order_id')->on('work_order')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trans_workorder_user');
    }
};
