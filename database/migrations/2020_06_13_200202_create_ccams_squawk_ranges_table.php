<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcamsSquawkRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ccams_squawk_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('first', 4)->comment('The first squawk in the range of the range');
            $table->string('last', 4)->comment('The last squawk in the range');
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
        Schema::dropIfExists('ccams_squawk_ranges');
    }
}
