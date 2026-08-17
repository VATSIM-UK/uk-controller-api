<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
