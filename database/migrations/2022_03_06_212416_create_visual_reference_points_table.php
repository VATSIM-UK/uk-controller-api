<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisualReferencePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visual_reference_points', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('The name of the VRP');
            $table->double('latitude', 10, 8)
                ->comment('The latitude of the VRP in decimal degrees');
            $table->double('longitude', 11, 8)
                ->comment('The longitude of the VRP in decimal degrees');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visual_reference_points');
    }
}
