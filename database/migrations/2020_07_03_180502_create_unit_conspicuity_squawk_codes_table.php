<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitConspicuitySquawkCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_conspicuity_squawk_codes', function (Blueprint $table) {
            $table->id();
            $table->string('unit')->comment('The unit to which the code applies');
            $table->string('code', 4)->comment('The squawk code');
            $table->timestamps();

            $table->index('unit');
            $table->unique(['unit', 'code'], 'unit_conspicuity_squawk_codes_unit_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_conspicuity_squawk_codes');
    }
}
