<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipe_equipment', function (Blueprint $table) {
            $table->bigInteger('operation_time')->nullable()->after('code');
        });
    }

    public function down(): void
    {
        Schema::table('tipe_equipment', function (Blueprint $table) {
            $table->dropColumn('operation_time');
        });
    }
};
