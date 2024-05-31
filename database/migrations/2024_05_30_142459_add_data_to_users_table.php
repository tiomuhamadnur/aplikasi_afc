<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->nullable();
            $table->string('no_hp')->nullable();
            $table->string('photo')->nullable();
            $table->bigInteger('gender_id')->unsigned()->nullable();
            $table->bigInteger('perusahaan_id')->unsigned()->nullable();
            $table->bigInteger('role_id')->unsigned()->nullable();
            $table->bigInteger('jabatan_id')->unsigned()->nullable();
            $table->bigInteger('tipe_employee_id')->unsigned()->nullable();
            $table->bigInteger('relasi_struktur_id')->unsigned()->nullable();

            $table->foreign('gender_id')->on('gender')->references('id');
            $table->foreign('perusahaan_id')->on('perusahaan')->references('id');
            $table->foreign('role_id')->on('role')->references('id');
            $table->foreign('jabatan_id')->on('jabatan')->references('id');
            $table->foreign('tipe_employee_id')->on('tipe_employee')->references('id');
            $table->foreign('relasi_struktur_id')->on('relasi_struktur')->references('id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        // Drop foreign keys first
        $table->dropForeign(['gender_id']);
        $table->dropForeign(['perusahaan_id']);
        $table->dropForeign(['role_id']);
        $table->dropForeign(['jabatan_id']);
        $table->dropForeign(['tipe_employee_id']);
        $table->dropForeign(['relasi_struktur_id']);

        // Then drop the columns
        $table->dropColumn('uuid');
        $table->dropColumn('no_hp');
        $table->dropColumn('gender_id');
        $table->dropColumn('perusahaan_id');
        $table->dropColumn('role_id');
        $table->dropColumn('jabatan_id');
        $table->dropColumn('tipe_employee_id');
        $table->dropColumn('relasi_struktur_id');
        });
    }
};
