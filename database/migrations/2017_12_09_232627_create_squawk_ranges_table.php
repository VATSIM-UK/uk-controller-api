<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSquawkRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'squawk_range',
            function (Blueprint $table) {
                $table->increments('id');
                $table->text('departure_ident')->nullable();
                $table->text('arrival_ident')->nullable();
                $table->text('start', 4);
                $table->text('stop', 4);
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('squawk_range');
    }
}
