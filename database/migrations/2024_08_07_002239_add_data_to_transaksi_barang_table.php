<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_barang', function (Blueprint $table) {
            $table->bigInteger('gangguan_id')->after('barang_id')->unsigned()->nullable();

            $table->foreign('gangguan_id')->on('gangguan')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_barang', function (Blueprint $table) {
            $table->dropForeign(['gangguan_id']);

            $table->dropColumn('gangguan_id');
        });
    }
};
