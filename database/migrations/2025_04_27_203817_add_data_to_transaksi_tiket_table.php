<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_tiket', function (Blueprint $table) {
            $table->string('file_name')->after('tap_out_station')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_tiket', function (Blueprint $table) {
            $table->dropColumn('file_name');
        });
    }
};
