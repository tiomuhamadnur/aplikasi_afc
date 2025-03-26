<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trans_gangguan_remedy', function (Blueprint $table) {
            $table->bigInteger('gangguan_lm_id')->after('gangguan_id')->unsigned()->nullable();

            $table->foreign('gangguan_lm_id')->on('gangguan_lm')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('trans_gangguan_remedy', function (Blueprint $table) {
            $table->dropForeign(['gangguan_lm_id']);

            $table->dropColumn('gangguan_lm_id');
        });
    }
};
