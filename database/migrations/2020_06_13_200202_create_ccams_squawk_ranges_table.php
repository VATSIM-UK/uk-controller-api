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
            $table->string('start')->comment('The start of the range');
            $table->string('stop')->comment('The end of the range');
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
