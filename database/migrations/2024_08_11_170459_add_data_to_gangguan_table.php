<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gangguan', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('classification');
            $table->bigInteger('status_id')->unsigned()->nullable();
            $table->bigInteger('classification_id')->unsigned()->nullable();

            $table->foreign('status_id')->on('status')->references('id');
            $table->foreign('classification_id')->on('classification')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('gangguan', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropForeign(['classification_id']);

            $table->dropColumn('status_id');
            $table->dropColumn('classification_id');
        });
    }
};
