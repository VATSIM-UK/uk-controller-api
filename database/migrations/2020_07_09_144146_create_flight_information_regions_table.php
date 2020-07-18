<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightInformationRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_information_regions', function (Blueprint $table) {
            $table->id();
            $table->string('identification_code', 4)->comment('The FIR identification code, e.g. EGTT');
            $table->timestamps();

            $table->unique('identification_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flight_information_regions');
    }
}
