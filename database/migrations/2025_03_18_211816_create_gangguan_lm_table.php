<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gangguan_lm', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique()->nullable();
            $table->uuid('uuid')->unique();
            $table->bigInteger('equipment_id')->unsigned()->nullable();
            $table->bigInteger('report_user_id')->unsigned()->nullable();
            $table->string('report_user')->nullable();
            $table->datetime('report_date')->nullable();
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->bigInteger('classification_id')->unsigned()->nullable();
            $table->bigInteger('lintas_id')->unsigned()->nullable();
            $table->bigInteger('line_id')->unsigned()->nullable();
            $table->enum('is_downtime', [1, 0])->nullable();
            $table->enum('is_delay', [1, 0])->nullable();
            $table->bigInteger('delay')->nullable()->default(0);
            $table->bigInteger('response_user_id')->unsigned()->nullable();
            $table->string('response_user')->nullable();
            $table->datetime('response_date')->nullable();
            $table->bigInteger('problem_id')->unsigned()->nullable();
            $table->text('problem_other')->nullable();
            $table->bigInteger('cause_id')->unsigned()->nullable();
            $table->text('cause_other')->nullable();
            $table->bigInteger('trans_gangguan_remedy_id')->unsigned()->nullable();
            $table->bigInteger('trans_gangguan_pending_id')->unsigned()->nullable();
            $table->enum('is_change_sparepart', [1, 0])->nullable();
            $table->enum('is_change_trainset', [1, 0])->nullable();
            $table->bigInteger('solved_user_id')->unsigned()->nullable();
            $table->string('solved_user')->nullable();
            $table->datetime('solved_date')->nullable();
            $table->string('photo_before')->nullable();
            $table->string('photo_after')->nullable();
            $table->bigInteger('status_id')->unsigned()->nullable();
            $table->bigInteger('response_time')->nullable();
            $table->bigInteger('resolution_time')->nullable();
            $table->bigInteger('total_time')->nullable();
            $table->text('remark')->nullable();
            $table->text('analysis')->nullable();
            $table->bigInteger('work_order_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('equipment_id')->on('equipment')->references('id');
            $table->foreign('report_user_id')->on('users')->references('id');
            $table->foreign('category_id')->on('category')->references('id');
            $table->foreign('classification_id')->on('classification')->references('id');
            $table->foreign('lintas_id')->on('relasi_area')->references('id');
            $table->foreign('line_id')->on('relasi_area')->references('id');
            $table->foreign('response_user_id')->on('users')->references('id');
            $table->foreign('problem_id')->on('problem')->references('id');
            $table->foreign('cause_id')->on('cause')->references('id');
            $table->foreign('trans_gangguan_remedy_id')->on('trans_gangguan_remedy')->references('id');
            $table->foreign('trans_gangguan_pending_id')->on('trans_gangguan_pending')->references('id');
            $table->foreign('solved_user_id')->on('users')->references('id');
            $table->foreign('status_id')->on('status')->references('id');
            $table->foreign('work_order_id')->on('work_order')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gangguan_lm');
    }
};
