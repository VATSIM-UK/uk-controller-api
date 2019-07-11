<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sid', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identifier')->comment('SID identifier, may or may not have a deprecation character');
            $table->unsignedInteger('airfield_id')->comment('The airfield the SID is tied to');
            $table->unsignedSmallInteger('initial_altitude')->comment('The initial altitude on the SID');
            $table->timestamps();

            $table->unique(['identifier', 'airfield_id']);
            $table->foreign('airfield_id')->references('id')->on('airfield');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sid');
    }
}
