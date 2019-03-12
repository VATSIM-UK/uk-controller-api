<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMslTmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('msl_tma', function (Blueprint $table) {
            $table->unsignedInteger('tma_id');
            $table->unsignedSmallInteger('msl');
            $table->timestamp('generated_at');

            $table->primary('tma_id');
            $table->foreign('tma_id')->references('id')->on('tma');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('msl_tma');
    }
}
