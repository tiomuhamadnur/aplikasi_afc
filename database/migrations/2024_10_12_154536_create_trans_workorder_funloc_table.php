<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trans_workorder_funloc', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('work_order_id')->unsigned()->nullable();
            $table->bigInteger('functional_location_id')->unsigned()->nullable();
            $table->string('status')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('work_order_id')->on('work_order')->references('id');
            $table->foreign('functional_location_id')->on('functional_location')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trans_workorder_funloc');
    }
};
