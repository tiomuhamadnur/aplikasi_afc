<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sam_card_history', function (Blueprint $table) {
            $table->bigInteger('equipment_id')->after('sam_card_id')->unsigned()->nullable();

            $table->foreign('equipment_id')->on('equipment')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('sam_card_history', function (Blueprint $table) {
            $table->dropForeign(['equipment_id']);

            $table->dropColumn('equipment_id');
        });
    }
};
