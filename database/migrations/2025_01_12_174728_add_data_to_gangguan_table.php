<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gangguan', function (Blueprint $table) {
            $table->boolean('is_downtime')->default(false)->nullable()->after('is_changed');
        });
    }

    public function down(): void
    {
        Schema::table('gangguan', function (Blueprint $table) {
            $table->dropColumn('is_downtime');
        });
    }
};
