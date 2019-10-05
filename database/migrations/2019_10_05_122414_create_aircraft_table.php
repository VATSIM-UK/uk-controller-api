<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAircraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aircraft', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->comment('The ICAO code for the aircraft');
            $table->unsignedTinyInteger('wake_category_id');
            $table->timestamps();

            // Keys
            $table->unique('code');
            $table->foreign('wake_category_id')
                ->references('id')
                ->on('wake_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aircraft');
    }
}
