<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitDiscreteSquawkCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_discrete_squawk_codes', function (Blueprint $table) {
            $table->id();
            $table->string('unit')->comment('The unit to which the code applies');
            $table->string('first', 4)->comment('The first squawk in the range');
            $table->string('last', 4)->comment('The last squawk in the range');
            $table->timestamps();

            $table->index('unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_discrete_squawk_codes');
    }
}
