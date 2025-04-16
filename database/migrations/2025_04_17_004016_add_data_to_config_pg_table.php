<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('config_pg', function (Blueprint $table) {
            $table->string('station_kue_id')->after('station_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('config_pg', function (Blueprint $table) {
            $table->dropColumn('station_kue_id');
        });
    }
};
