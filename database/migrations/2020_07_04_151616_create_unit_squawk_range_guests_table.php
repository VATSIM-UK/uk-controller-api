<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitSquawkRangeGuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_squawk_range_guests', function (Blueprint $table) {
            $table->id();
            $table->string('primary_unit')->comment('The unit that owns the squawk range');
            $table->string('guest_unit')->comment('The unit that is a squawk range guest of the other');
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
        Schema::dropIfExists('unit_squawk_range_guests');
    }
}
