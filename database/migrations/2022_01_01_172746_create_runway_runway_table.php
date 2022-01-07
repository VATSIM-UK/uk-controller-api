<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRunwayRunwayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('runway_runway', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('first_runway_id')->comment('The first runway');
            $table->unsignedBigInteger('second_runway_id')->comment('The second runway');
            $table->timestamps();

            $table->unique(['first_runway_id', 'second_runway_id'], 'runway_pairs_unique');
            $table->foreign('first_runway_id')->references('id')->on('runways')->cascadeOnDelete();
            $table->foreign('second_runway_id')->references('id')->on('runways')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('runway_runway');
    }
}
