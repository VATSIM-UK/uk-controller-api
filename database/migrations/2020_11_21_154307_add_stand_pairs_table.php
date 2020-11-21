<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStandPairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stand_pairs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stand_id')->comment('The first stand in the pair');
            $table->unsignedBigInteger('paired_stand_id')->comment('The second stand in the pair');

            $table->unique(['stand_id', 'paired_stand_id']);
            $table->foreign('stand_id')->references('id')->on('stands')->cascadeOnDelete();
            $table->foreign('paired_stand_id')->references('id')->on('stands')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stand_pairs');
    }
}
