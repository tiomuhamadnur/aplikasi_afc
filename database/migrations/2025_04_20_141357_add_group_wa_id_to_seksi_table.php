<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seksi', function (Blueprint $table) {
            $table->string('group_wa_id')->after('code')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('seksi', function (Blueprint $table) {
            $table->dropColumn('group_wa_id');
        });
    }
};
