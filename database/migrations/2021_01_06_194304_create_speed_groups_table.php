<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpeedGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speed_groups', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('String key for easier identification');
            $table->integer('airfield_id')->comment('The airfield where the speed group applies');
            $table->timestamps();

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
        Schema::dropIfExists('speed_groups');
    }
}
