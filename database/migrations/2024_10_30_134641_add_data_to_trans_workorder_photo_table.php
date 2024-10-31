<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trans_workorder_photo', function (Blueprint $table) {
            $table->string('description')->after('photo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('trans_workorder_photo', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
