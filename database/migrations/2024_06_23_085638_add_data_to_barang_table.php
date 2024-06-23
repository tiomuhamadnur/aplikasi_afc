<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->bigInteger('relasi_struktur_id')->after('tipe_barang_id')->unsigned()->nullable();

            $table->foreign('relasi_struktur_id')->on('relasi_struktur')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropForeign(['relasi_struktur_id']);

            $table->dropColumn('relasi_struktur_id');
        });
    }
};
