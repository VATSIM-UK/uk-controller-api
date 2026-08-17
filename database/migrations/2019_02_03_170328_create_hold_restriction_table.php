<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHoldRestrictionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hold_restriction', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('hold_id')->comment('The hold the restriction applies to');
            $table->json('restriction')->comment('JSON containing information about the restriction');
            $table->timestamps();

            $table->foreign('hold_id')->references('id')->on('hold');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hold_restriction');
    }
}
