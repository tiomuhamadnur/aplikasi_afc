<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sam_card_history', function (Blueprint $table) {
            $table->string('old_uid')->nullable()->after('type');
            $table->string('photo')->nullable()->after('type');
            $table->bigInteger('old_sam_card_id')->unsigned()->nullable();

            $table->foreign('old_sam_card_id')->on('sam_card')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('sam_card_history', function (Blueprint $table) {
            $table->dropColumn('old_uid');
            $table->dropColumn('photo');
            $table->dropForeign(['old_sam_card_id']);

            $table->dropColumn('old_sam_card_id');
        });
    }
};
