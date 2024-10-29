<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checksheet', function (Blueprint $table) {
            $table->bigInteger('functional_location_id')->after('equipment_id')->unsigned()->nullable();

            $table->foreign('functional_location_id')->on('functional_location')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('checksheet', function (Blueprint $table) {
            $table->dropForeign(['functional_location_id']);

            $table->dropColumn('functional_location_id');
        });
    }
};
