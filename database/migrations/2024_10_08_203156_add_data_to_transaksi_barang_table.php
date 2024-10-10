<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_barang', function (Blueprint $table) {
            $table->bigInteger('work_order_id')->unsigned()->nullable();

            $table->foreign('work_order_id')->on('work_order')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_barang', function (Blueprint $table) {
            $table->dropForeign(['work_order_id']);
            $table->dropColumn('work_order_id');
        });
    }
};
