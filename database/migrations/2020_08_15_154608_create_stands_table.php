<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stands', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('airfield_id')->comment('The airfield that the stand is at');
            $table->string('identifier')->comment('The stand identifier');
            $table->string('latitude')->comment('The stands latitude');
            $table->string('longitude')->comment('The stands longitude');
            $table->timestamps();

            $table->foreign('airfield_id')->references('id')->on('airfield')->onDelete('cascade');
            $table->unique(['airfield_id', 'identifier']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stands');
    }
}
