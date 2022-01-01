<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRunwaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('runways', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('airfield_id')->comment('The airfield that has the runway');
            $table->string('identifier')->comment('The runway identifier');
            $table->double('threshold_latitude', 10, 8)->comment('The latitude of the runway threshold');
            $table->double('threshold_longitude', 11, 8)->comment('The longitude of the runway threshold');
            $table->unsignedMediumInteger('heading')->comment('The runway heading');
            $table->timestamps();

            $table->index('identifier');
            $table->unique(['airfield_id', 'identifier']);
            $table->foreign('airfield_id')->references('id')->on('airfield')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('runways');
    }
}
