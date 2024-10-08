<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gangguan', function (Blueprint $table) {
            $table->bigInteger('cause_id')->unsigned()->nullable();
            $table->string('cause_other')->nullable();
            $table->dropColumn('action');
            $table->dropColumn('pending_start_date');
            $table->dropColumn('pending_finish_date');
            $table->dropForeign(['barang_id']);
            $table->dropColumn('barang_id');
            $table->dropColumn('qty');
            $table->longText('remark')->nullable();
            $table->bigInteger('trans_gangguan_remedy_id')->unsigned()->nullable();
            $table->bigInteger('trans_gangguan_pending_id')->unsigned()->nullable();

            $table->foreign('cause_id')->on('cause')->references('id');
            $table->foreign('trans_gangguan_remedy_id')->on('trans_gangguan_remedy')->references('id');
            $table->foreign('trans_gangguan_pending_id')->on('trans_gangguan_pending')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('gangguan', function (Blueprint $table) {
            // Revert foreign keys
            $table->dropForeign(['cause_id']);
            $table->dropForeign(['trans_gangguan_remedy_id']);
            $table->dropForeign(['trans_gangguan_pending_id']);

            // Revert column changes
            $table->dropColumn('cause_id');
            $table->dropColumn('cause_other');
            $table->longText('action')->nullable();
            $table->dropColumn('remark');
            $table->dropColumn('trans_gangguan_remedy_id');
            $table->dropColumn('trans_gangguan_pending_id');

            // Re-add dropped columns
            $table->dateTime('pending_start_date')->nullable();
            $table->dateTime('pending_finish_date')->nullable();
            $table->bigInteger('barang_id')->unsigned();
            $table->integer('qty')->nullable();

            // Re-add foreign key for barang_id
            $table->foreign('barang_id')->references('id')->on('barang');
        });
    }
};
