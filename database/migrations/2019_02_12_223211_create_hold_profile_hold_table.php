<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoldProfileHoldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hold_profile_hold', function (Blueprint $table) {
            $table->unsignedInteger('hold_profile_id');
            $table->unsignedInteger('hold_id');


            $table->foreign('hold_profile_id')->references('id')->on('hold_profile');
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
        Schema::dropIfExists('hold_profile_hold');
    }
}
