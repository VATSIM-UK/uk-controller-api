<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControllerPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controller_positions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('callsign');
            $table->decimal('frequency', 6, 3);
            $table->timestamps();

            $table->unique('callsign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('controller_positions');
    }
}
