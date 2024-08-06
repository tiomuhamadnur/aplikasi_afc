<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gangguan', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique()->nullable();
            $table->uuid('uuid')->unique();
            $table->bigInteger('equipment_id')->unsigned()->nullable();
            $table->bigInteger('report_user_id')->unsigned()->nullable();
            $table->bigInteger('response_user_id')->unsigned()->nullable();
            $table->bigInteger('solved_user_id')->unsigned()->nullable();
            $table->string('report_by')->nullable();
            $table->string('response_by')->nullable();
            $table->string('solved_by')->nullable();
            $table->datetime('report_date')->nullable();
            $table->datetime('response_date')->nullable();
            $table->datetime('solved_date')->nullable();
            $table->text('problem')->nullable();
            $table->string('category')->nullable();
            $table->string('classification')->nullable();
            $table->text('action')->nullable();
            $table->text('analysis')->nullable();
            $table->datetime('pending_start_date')->nullable();
            $table->datetime('pending_finish_date')->nullable();
            $table->string('response_time')->nullable();
            $table->string('resolution_time')->nullable();
            $table->text('photo')->nullable();
            $table->text('photo_after')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_changed')->default(false)->nullable();
            $table->bigInteger('barang_id')->unsigned()->nullable();
            $table->integer('qty')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('equipment_id')->on('equipment')->references('id');
            $table->foreign('report_user_id')->on('users')->references('id');
            $table->foreign('response_user_id')->on('users')->references('id');
            $table->foreign('solved_user_id')->on('users')->references('id');
            $table->foreign('barang_id')->on('barang')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gangguan');
    }
};
