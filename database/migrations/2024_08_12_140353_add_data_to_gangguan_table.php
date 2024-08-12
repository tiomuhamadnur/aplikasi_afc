<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gangguan', function (Blueprint $table) {
            $table->renameColumn('problem', 'problem_other');
            $table->bigInteger('problem_id')->unsigned()->nullable();

            $table->foreign('problem_id')->on('problem')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('gangguan', function (Blueprint $table) {
            $table->renameColumn('problem_other', 'problem');
            $table->dropForeign(['problem_id']);

            $table->dropColumn('problem_id');
        });
    }
};
