<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_absorption', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('project_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('value')->nullable();
            $table->date('activity_date')->nullable();
            $table->string('po_number_sap')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('attachment')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('status_budgeting_id')->unsigned()->nullable();
            $table->integer('termin')->nullable();
            $table->string('remark')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('project_id')->on('project')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->foreign('status_budgeting_id')->on('status_budgeting')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_absorption');
    }
};
