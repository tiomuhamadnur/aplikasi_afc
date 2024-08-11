<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gangguan', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->bigInteger('category_id')->unsigned()->nullable();

            $table->foreign('category_id')->on('category')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('gangguan', function (Blueprint $table) {
            $table->dropForeign(['category_id']);

            $table->dropColumn('category_id');
        });
    }
};
